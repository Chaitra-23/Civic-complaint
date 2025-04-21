const express = require('express');
const router = express.Router();
const { pool } = require('../db');
const { authenticateToken } = require('../middleware/auth');

// Get all complaints with optional filters
router.get('/', authenticateToken, async (req, res) => {
  const { status, priority, userId } = req.query;
  let query = 'SELECT * FROM complaints';
  const params = [];
  let paramCount = 1;

  if (status || priority || userId) {
    query += ' WHERE';
    const conditions = [];

    if (status) {
      conditions.push(`status = $${paramCount}`);
      params.push(status);
      paramCount++;
    }

    if (priority) {
      conditions.push(`priority = $${paramCount}`);
      params.push(priority);
      paramCount++;
    }

    if (userId) {
      conditions.push(`user_id = $${paramCount}`);
      params.push(userId);
      paramCount++;
    }

    query += ' ' + conditions.join(' AND ');
  }

  query += ' ORDER BY created_at DESC';

  try {
    const result = await pool.query(query, params);
    res.json(result.rows);
  } catch (error) {
    console.error('Error fetching complaints:', error);
    res.status(500).json({ error: 'Failed to fetch complaints' });
  }
});

// Get single complaint
router.get('/:id', authenticateToken, async (req, res) => {
  const { id } = req.params;

  try {
    const result = await pool.query(
      'SELECT * FROM complaints WHERE complaint_id = $1',
      [id]
    );

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Complaint not found' });
    }

    res.json(result.rows[0]);
  } catch (error) {
    console.error('Error fetching complaint:', error);
    res.status(500).json({ error: 'Failed to fetch complaint' });
  }
});

// Create new complaint
router.post('/', authenticateToken, async (req, res) => {
  const { title, description, priority } = req.body;
  const userId = req.user.userId;

  try {
    const result = await pool.query(
      'INSERT INTO complaints (user_id, title, description, priority, status) VALUES ($1, $2, $3, $4, $5) RETURNING *',
      [userId, title, description, priority, 'pending']
    );

    res.status(201).json(result.rows[0]);
  } catch (error) {
    console.error('Error creating complaint:', error);
    res.status(500).json({ error: 'Failed to create complaint' });
  }
});

// Update complaint status
router.put('/:id', authenticateToken, async (req, res) => {
  const { id } = req.params;
  const { status } = req.body;

  try {
    const result = await pool.query(
      'UPDATE complaints SET status = $1 WHERE complaint_id = $2 RETURNING *',
      [status, id]
    );

    if (result.rows.length === 0) {
      return res.status(404).json({ error: 'Complaint not found' });
    }

    res.json(result.rows[0]);
  } catch (error) {
    console.error('Error updating complaint:', error);
    res.status(500).json({ error: 'Failed to update complaint' });
  }
});

// Add comment to complaint
router.post('/:id/comments', authenticateToken, async (req, res) => {
  const { id } = req.params;
  const { comment_text } = req.body;
  const userId = req.user.userId;

  try {
    const result = await pool.query(
      'INSERT INTO comments (complaint_id, user_id, comment_text) VALUES ($1, $2, $3) RETURNING *',
      [id, userId, comment_text]
    );

    res.status(201).json(result.rows[0]);
  } catch (error) {
    console.error('Error adding comment:', error);
    res.status(500).json({ error: 'Failed to add comment' });
  }
});

module.exports = router; 