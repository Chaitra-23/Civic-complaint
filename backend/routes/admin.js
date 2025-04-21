const express = require('express');
const router = express.Router();
const { pool } = require('../db');
const { authenticateToken, isAdmin } = require('../middleware/auth');

// Get all users
router.get('/users', authenticateToken, isAdmin, async (req, res) => {
  try {
    const result = await pool.query(
      'SELECT user_id, username, email, role FROM users ORDER BY created_at DESC'
    );
    res.json(result.rows);
  } catch (error) {
    console.error('Error fetching users:', error);
    res.status(500).json({ error: 'Failed to fetch users' });
  }
});

// Update user role
router.put('/users/:userId', authenticateToken, isAdmin, async (req, res) => {
  const { userId } = req.params;
  const { role } = req.body;

  try {
    await pool.query(
      'UPDATE users SET role = $1 WHERE user_id = $2',
      [role, userId]
    );
    res.json({ message: 'User role updated successfully' });
  } catch (error) {
    console.error('Error updating user role:', error);
    res.status(500).json({ error: 'Failed to update user role' });
  }
});

// Delete user
router.delete('/users/:userId', authenticateToken, isAdmin, async (req, res) => {
  const { userId } = req.params;

  try {
    await pool.query('DELETE FROM users WHERE user_id = $1', [userId]);
    res.json({ message: 'User deleted successfully' });
  } catch (error) {
    console.error('Error deleting user:', error);
    res.status(500).json({ error: 'Failed to delete user' });
  }
});

// Get statistics
router.get('/statistics', authenticateToken, isAdmin, async (req, res) => {
  try {
    // Get total users
    const usersResult = await pool.query('SELECT COUNT(*) FROM users');
    const totalUsers = parseInt(usersResult.rows[0].count);

    // Get total complaints
    const complaintsResult = await pool.query('SELECT COUNT(*) FROM complaints');
    const totalComplaints = parseInt(complaintsResult.rows[0].count);

    // Get complaints by status
    const statusResult = await pool.query(
      'SELECT status, COUNT(*) FROM complaints GROUP BY status'
    );
    const complaintsByStatus = {};
    statusResult.rows.forEach(row => {
      complaintsByStatus[row.status] = parseInt(row.count);
    });

    // Get complaints by priority
    const priorityResult = await pool.query(
      'SELECT priority, COUNT(*) FROM complaints GROUP BY priority'
    );
    const complaintsByPriority = {};
    priorityResult.rows.forEach(row => {
      complaintsByPriority[row.priority] = parseInt(row.count);
    });

    res.json({
      totalUsers,
      totalComplaints,
      complaintsByStatus,
      complaintsByPriority
    });
  } catch (error) {
    console.error('Error fetching statistics:', error);
    res.status(500).json({ error: 'Failed to fetch statistics' });
  }
});

module.exports = router; 