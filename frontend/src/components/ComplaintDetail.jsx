import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import {
  Container,
  Paper,
  Typography,
  Box,
  Chip,
  Button,
  TextField,
  List,
  ListItem,
  ListItemText,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Divider,
} from '@mui/material';
import { ArrowBack as ArrowBackIcon } from '@mui/icons-material';
import { complaints } from '../services/api';
import { useAuth } from '../context/AuthContext';
import { toast } from 'react-toastify';

export default function ComplaintDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user } = useAuth();
  const [complaint, setComplaint] = useState(null);
  const [comment, setComment] = useState('');
  const [newStatus, setNewStatus] = useState('');

  const fetchComplaint = async () => {
    try {
      const response = await complaints.getById(id);
      setComplaint(response.data);
      setNewStatus(response.data.status);
    } catch (error) {
      toast.error('Failed to fetch complaint details');
      navigate('/complaints');
    }
  };

  useEffect(() => {
    fetchComplaint();
  }, [id]);

  const handleAddComment = async (e) => {
    e.preventDefault();
    try {
      await complaints.addComment(id, comment);
      setComment('');
      fetchComplaint();
      toast.success('Comment added successfully');
    } catch (error) {
      toast.error('Failed to add comment');
    }
  };

  const handleStatusUpdate = async () => {
    try {
      await complaints.updateStatus(id, newStatus);
      fetchComplaint();
      toast.success('Status updated successfully');
    } catch (error) {
      toast.error('Failed to update status');
    }
  };

  if (!complaint) {
    return null;
  }

  return (
    <Container maxWidth="md" sx={{ mt: 4 }}>
      <Button
        startIcon={<ArrowBackIcon />}
        onClick={() => navigate('/complaints')}
        sx={{ mb: 2 }}
      >
        Back to Complaints
      </Button>

      <Paper elevation={2} sx={{ p: 3 }}>
        <Typography variant="h4" gutterBottom>
          {complaint.title}
        </Typography>

        <Box sx={{ mb: 3, display: 'flex', gap: 1 }}>
          <Chip
            label={complaint.status.replace('_', ' ')}
            color={complaint.status === 'resolved' ? 'success' : 'warning'}
          />
          <Chip label={complaint.priority} color="primary" />
        </Box>

        <Typography variant="body1" sx={{ mb: 3 }}>
          {complaint.description}
        </Typography>

        <Typography variant="body2" color="text.secondary" sx={{ mb: 3 }}>
          Submitted by {complaint.username} on{' '}
          {new Date(complaint.created_at).toLocaleDateString()}
        </Typography>

        {user?.role === 'admin' && (
          <Box sx={{ mb: 3, display: 'flex', gap: 2, alignItems: 'center' }}>
            <FormControl sx={{ minWidth: 200 }}>
              <InputLabel>Status</InputLabel>
              <Select
                value={newStatus}
                label="Status"
                onChange={(e) => setNewStatus(e.target.value)}
              >
                <MenuItem value="pending">Pending</MenuItem>
                <MenuItem value="in_progress">In Progress</MenuItem>
                <MenuItem value="resolved">Resolved</MenuItem>
                <MenuItem value="rejected">Rejected</MenuItem>
              </Select>
            </FormControl>
            <Button
              variant="contained"
              onClick={handleStatusUpdate}
              disabled={newStatus === complaint.status}
            >
              Update Status
            </Button>
          </Box>
        )}

        <Divider sx={{ my: 3 }} />

        <Typography variant="h6" gutterBottom>
          Comments
        </Typography>

        <Box component="form" onSubmit={handleAddComment} sx={{ mb: 3 }}>
          <TextField
            fullWidth
            multiline
            rows={3}
            label="Add a comment"
            value={comment}
            onChange={(e) => setComment(e.target.value)}
            sx={{ mb: 1 }}
          />
          <Button
            type="submit"
            variant="contained"
            disabled={!comment.trim()}
          >
            Add Comment
          </Button>
        </Box>

        <List>
          {complaint.comments.map((comment) => (
            <ListItem key={comment.comment_id} divider>
              <ListItemText
                primary={comment.comment_text}
                secondary={
                  <>
                    By {comment.username} on{' '}
                    {new Date(comment.created_at).toLocaleDateString()}
                  </>
                }
              />
            </ListItem>
          ))}
        </List>
      </Paper>
    </Container>
  );
} 