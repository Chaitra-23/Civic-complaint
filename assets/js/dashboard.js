/**
 * Civic Complaints System - Dashboard Charts
 * This file contains JavaScript for rendering interactive charts on the admin dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the admin dashboard page
    if (document.getElementById('complaintsChart') && document.getElementById('departmentChart')) {
        // Fetch data for charts
        fetchChartData();
    }
});

/**
 * Fetch chart data from the server
 */
function fetchChartData() {
    fetch('../api/dashboard_stats.php')
        .then(response => response.json())
        .then(data => {
            renderComplaintsChart(data.statusData);
            renderDepartmentChart(data.departmentData);
            renderPriorityChart(data.priorityData);
            renderMonthlyChart(data.monthlyData);
            updateStatCards(data.stats);
        })
        .catch(error => {
            console.error('Error fetching chart data:', error);
        });
}

/**
 * Render complaints by status chart
 */
function renderComplaintsChart(data) {
    const ctx = document.getElementById('complaintsChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'In Progress', 'Resolved', 'Rejected'],
            datasets: [{
                data: [
                    data.pending || 0,
                    data.in_progress || 0,
                    data.resolved || 0,
                    data.rejected || 0
                ],
                backgroundColor: [
                    '#ffc107', // Warning - Pending
                    '#17a2b8', // Info - In Progress
                    '#28a745', // Success - Resolved
                    '#dc3545'  // Danger - Rejected
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Complaints by Status',
                    font: {
                        size: 16
                    }
                }
            }
        }
    });
}

/**
 * Render complaints by department chart
 */
function renderDepartmentChart(data) {
    const ctx = document.getElementById('departmentChart').getContext('2d');
    
    const labels = data.map(item => item.name);
    const values = data.map(item => item.count);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Number of Complaints',
                data: values,
                backgroundColor: '#3498db',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Complaints by Department',
                    font: {
                        size: 16
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}

/**
 * Render complaints by priority chart
 */
function renderPriorityChart(data) {
    const ctx = document.getElementById('priorityChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['High', 'Medium', 'Low'],
            datasets: [{
                data: [
                    data.high || 0,
                    data.medium || 0,
                    data.low || 0
                ],
                backgroundColor: [
                    '#dc3545', // Danger - High
                    '#ffc107', // Warning - Medium
                    '#17a2b8'  // Info - Low
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Complaints by Priority',
                    font: {
                        size: 16
                    }
                }
            }
        }
    });
}

/**
 * Render monthly complaints chart
 */
function renderMonthlyChart(data) {
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    
    const months = data.map(item => item.month);
    const counts = data.map(item => item.count);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Complaints',
                data: counts,
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Complaints Trend',
                    font: {
                        size: 16
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}

/**
 * Update dashboard stat cards with latest data
 */
function updateStatCards(stats) {
    // Update total complaints
    const totalElement = document.getElementById('totalComplaints');
    if (totalElement) {
        totalElement.textContent = stats.total || 0;
    }
    
    // Update resolved complaints
    const resolvedElement = document.getElementById('resolvedComplaints');
    if (resolvedElement) {
        resolvedElement.textContent = stats.resolved || 0;
    }
    
    // Update pending complaints
    const pendingElement = document.getElementById('pendingComplaints');
    if (pendingElement) {
        pendingElement.textContent = stats.pending || 0;
    }
    
    // Update resolution rate
    const rateElement = document.getElementById('resolutionRate');
    if (rateElement) {
        const rate = stats.total > 0 ? Math.round((stats.resolved / stats.total) * 100) : 0;
        rateElement.textContent = rate + '%';
    }
    
    // Update average resolution time
    const timeElement = document.getElementById('avgResolutionTime');
    if (timeElement) {
        timeElement.textContent = stats.avg_resolution_time || 'N/A';
    }
}