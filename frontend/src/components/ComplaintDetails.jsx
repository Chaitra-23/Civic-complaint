import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import {
  Container,
  Paper,
  Typography,
  Button,
  Grid,
  TextField,
  List,
  ListItem,
  ListItemText,
  Divider,
} from '@mui/material';
import { complaints } from '../services/api';
import { useAuth } from '../context/AuthContext';
import { toast } from 'react-toastify';

export default function ComplaintDetails() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user } = useAuth();
  const [complaint, setComplaint] = useState(null);
  const [comments, setComments] = useState([]);
  const [newComment, setNewComment] = useState('');

  useEffect(() => {
    fetchComplaint();
  }, [id]);

  const fetchComplaint = async () => {
    try {
      const response = await complaints.getById(id);
      setComplaint(response.data);
      setComments(response.data.comments || []);
    } catch (error) {
      console.error('Error fetching complaint:', error);
      toast.error('Failed to fetch complaint details');
    }
  };

  const handleStatusChange = async (newStatus) => {
    try {
      await complaints.updateStatus(id, newStatus);
      setComplaint({ ...complaint, status: newStatus });
      toast.success('Status updated successfully');
    } catch (error) {
      console.error('Error updating status:', error);
      toast.error('Failed to update status');
    }
  };

  const handleAddComment = async (e) => {
    e.preventDefault();
    try {
      await complaints.addComment(id, newComment);
      setNewComment('');
      fetchComplaint();
      toast.success('Comment added successfully');
    } catch (error) {
      console.error('Error adding comment:', error);
      toast.error('Failed to add comment');
    }
  };

  if (!complaint) {
    return <div>Loading...</div>;
  }

  return (
    <Container maxWidth="md" sx={{ mt: 4 }}>
      <Paper elevation={2} sx={{ p: 4 }}>
        <Grid container spacing={3}>
          <Grid item xs={12}>
            <Typography variant="h4" gutterBottom>
              {complaint.title}
            </Typography>
          </Grid>
          <Grid item xs={12}>
            <Typography variant="body1" paragraph>
              {complaint.description}
            </Typography>
          </Grid>
          <Grid item xs={12} md={4}>
            <Typography variant="subtitle1">
              Status: {complaint.status}
            </Typography>
          </Grid>
          <Grid item xs={12} md={4}>
            <Typography variant="subtitle1">
              Priority: {complaint.priority}
            </Typography>
          </Grid>
          <Grid item xs={12} md={4}>
            <Typography variant="subtitle1">
              Created: {new Date(complaint.created_at).toLocaleDateString()}
            </Typography>
          </Grid>

          {user.role === 'admin' && (
            <Grid item xs={12}>
              <Button
                variant="contained"
                color="primary"
                onClick={() => handleStatusChange('in_progress')}
                disabled={complaint.status === 'in_progress'}
              >
                Mark In Progress
              </Button>
              <Button
                variant="contained"
                color="success"
                onClick={() => handleStatusChange('resolved')}
                disabled={complaint.status === 'resolved'}
                sx={{ ml: 2 }}
              >
                Mark Resolved
              </Button>
            </Grid>
          )}

          <Grid item xs={12}>
            <Typography variant="h6" gutterBottom>
              Comments
            </Typography>
            <List>
              {comments.map((comment, index) => (
                <React.Fragment key={index}>
                  <ListItem>
                    <ListItemText
                      primary={comment.comment_text}
                      secondary={`By ${comment.username} on ${new Date(
                        comment.created_at
                      ).toLocaleDateString()}`}
                    />
                  </ListItem>
                  {index < comments.length - 1 && <Divider />}
                </React.Fragment>
              ))}
            </List>
          </Grid>

          <Grid item xs={12}>
            <form onSubmit={handleAddComment}>
              <TextField
                fullWidth
                label="Add a comment"
                value={newComment}
                onChange={(e) => setNewComment(e.target.value)}
                multiline
                rows={2}
                required
              />
              <Button
                type="submit"
                variant="contained"
                color="primary"
                sx={{ mt: 2 }}
              >
                Add Comment
              </Button>
            </form>
          </Grid>
        </Grid>
      </Paper>
    </Container>
  );
} 