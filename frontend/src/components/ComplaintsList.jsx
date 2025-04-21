import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import {
  Container,
  Paper,
  Typography,
  List,
  ListItem,
  ListItemText,
  Button,
  Chip,
  Box,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
} from '@mui/material';
import { Add as AddIcon } from '@mui/icons-material';
import { complaints } from '../services/api';
import { useAuth } from '../context/AuthContext';
import { toast } from 'react-toastify';

export default function ComplaintsList() {
  const [complaintsList, setComplaintsList] = useState([]);
  const [status, setStatus] = useState('');
  const [priority, setPriority] = useState('');
  const navigate = useNavigate();
  const { user } = useAuth();

  const fetchComplaints = async () => {
    try {
      const filters = {};
      if (status) filters.status = status;
      if (priority) filters.priority = priority;
      
      const response = await complaints.getAll(filters);
      setComplaintsList(response.data);
    } catch (error) {
      toast.error('Failed to fetch complaints');
    }
  };

  useEffect(() => {
    fetchComplaints();
  }, [status, priority]);

  const getStatusColor = (status) => {
    const colors = {
      pending: 'warning',
      in_progress: 'info',
      resolved: 'success',
      rejected: 'error',
    };
    return colors[status] || 'default';
  };

  const getPriorityColor = (priority) => {
    const colors = {
      low: 'success',
      medium: 'warning',
      high: 'error',
    };
    return colors[priority] || 'default';
  };

  return (
    <Container maxWidth="md" sx={{ mt: 4 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 3 }}>
        <Typography variant="h4">Complaints</Typography>
        <Button
          variant="contained"
          startIcon={<AddIcon />}
          onClick={() => navigate('/complaints/new')}
        >
          New Complaint
        </Button>
      </Box>

      <Box sx={{ display: 'flex', gap: 2, mb: 3 }}>
        <FormControl sx={{ minWidth: 120 }}>
          <InputLabel>Status</InputLabel>
          <Select
            value={status}
            label="Status"
            onChange={(e) => setStatus(e.target.value)}
          >
            <MenuItem value="">All</MenuItem>
            <MenuItem value="pending">Pending</MenuItem>
            <MenuItem value="in_progress">In Progress</MenuItem>
            <MenuItem value="resolved">Resolved</MenuItem>
            <MenuItem value="rejected">Rejected</MenuItem>
          </Select>
        </FormControl>

        <FormControl sx={{ minWidth: 120 }}>
          <InputLabel>Priority</InputLabel>
          <Select
            value={priority}
            label="Priority"
            onChange={(e) => setPriority(e.target.value)}
          >
            <MenuItem value="">All</MenuItem>
            <MenuItem value="low">Low</MenuItem>
            <MenuItem value="medium">Medium</MenuItem>
            <MenuItem value="high">High</MenuItem>
          </Select>
        </FormControl>
      </Box>

      <Paper elevation={2}>
        <List>
          {complaintsList.map((complaint) => (
            <ListItem
              key={complaint.complaint_id}
              divider
              button
              onClick={() => navigate(`/complaints/${complaint.complaint_id}`)}
            >
              <ListItemText
                primary={complaint.title}
                secondary={
                  <Box sx={{ mt: 1 }}>
                    <Typography variant="body2" color="text.secondary">
                      By {complaint.username} on{' '}
                      {new Date(complaint.created_at).toLocaleDateString()}
                    </Typography>
                    <Box sx={{ mt: 1, display: 'flex', gap: 1 }}>
                      <Chip
                        label={complaint.status.replace('_', ' ')}
                        color={getStatusColor(complaint.status)}
                        size="small"
                      />
                      <Chip
                        label={complaint.priority}
                        color={getPriorityColor(complaint.priority)}
                        size="small"
                      />
                    </Box>
                  </Box>
                }
              />
            </ListItem>
          ))}
        </List>
      </Paper>
    </Container>
  );
} 