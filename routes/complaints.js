const express = require('express');
const router = express.Router();
const pool = require('../db');
const authenticateToken = require('../middleware/auth');

// Get all complaints (with filters)
router.get('/', authenticateToken, async (req, res) => {
    try {
        const { status, priority } = req.query;
        let query = 'SELECT c.*, u.username FROM complaints c JOIN users u ON c.user_id = u.user_id';
        const params = [];

        if (status) {
            query += ' WHERE c.status = ?';
            params.push(status);
        }

        if (priority) {
            query += status ? ' AND' : ' WHERE';
            query += ' c.priority = ?';
            params.push(priority);
        }

        query += ' ORDER BY c.created_at DESC';

        const [complaints] = await pool.execute(query, params);
        res.json(complaints);
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Error fetching complaints' });
    }
});

// Get a single complaint
router.get('/:id', authenticateToken, async (req, res) => {
    try {
        const [complaints] = await pool.execute(
            'SELECT c.*, u.username FROM complaints c JOIN users u ON c.user_id = u.user_id WHERE c.complaint_id = ?',
            [req.params.id]
        );

        if (complaints.length === 0) {
            return res.status(404).json({ message: 'Complaint not found' });
        }

        const [comments] = await pool.execute(
            'SELECT cm.*, u.username FROM comments cm JOIN users u ON cm.user_id = u.user_id WHERE cm.complaint_id = ? ORDER BY cm.created_at ASC',
            [req.params.id]
        );

        res.json({
            ...complaints[0],
            comments
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Error fetching complaint' });
    }
});

// Create a new complaint
router.post('/', authenticateToken, async (req, res) => {
    try {
        const { title, description, priority } = req.body;
        const [result] = await pool.execute(
            'INSERT INTO complaints (user_id, title, description, priority) VALUES (?, ?, ?, ?)',
            [req.user.userId, title, description, priority || 'medium']
        );

        res.status(201).json({
            message: 'Complaint created successfully',
            complaintId: result.insertId
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Error creating complaint' });
    }
});

// Update a complaint (admin only)
router.put('/:id', authenticateToken, async (req, res) => {
    try {
        if (req.user.role !== 'admin') {
            return res.status(403).json({ message: 'Only admins can update complaints' });
        }

        const { status } = req.body;
        await pool.execute(
            'UPDATE complaints SET status = ? WHERE complaint_id = ?',
            [status, req.params.id]
        );

        res.json({ message: 'Complaint updated successfully' });
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Error updating complaint' });
    }
});

// Add a comment to a complaint
router.post('/:id/comments', authenticateToken, async (req, res) => {
    try {
        const { comment_text } = req.body;
        const [result] = await pool.execute(
            'INSERT INTO comments (complaint_id, user_id, comment_text) VALUES (?, ?, ?)',
            [req.params.id, req.user.userId, comment_text]
        );

        res.status(201).json({
            message: 'Comment added successfully',
            commentId: result.insertId
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Error adding comment' });
    }
});

module.exports = router; 