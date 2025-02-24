const express = require('express');
const cors = require('cors');
const jwt = require('jsonwebtoken');
const mysql = require('mysql2/promise');
require('dotenv').config();

const app = express();

// Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

const corsOptions = {
    origin: 'http://localhost:5173',
    methods: ['GET', 'POST', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization'],
    credentials: true,
};

app.use(cors(corsOptions));

const pool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'admin_db',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

pool.getConnection()
    .then(connection => {
        console.log('Connected to MySQL database');
        connection.release();
    })
    .catch(err => {
        console.error('Error connecting to the database:', err);
    });

    app.post('/api/administrators', async (req, res) => {
        try {
            const { email, password } = req.body;
    
            if (!email || !password) {
                return res.status(400).json({
                    success: false,
                    message: 'Please provide both email and password'
                });
            }
    
            const [admins] = await pool.execute(
                'SELECT * FROM administrators WHERE email = ?',
                [email]
            );
    
            const admin = admins[0];
            if (!admin) {
                return res.status(401).json({
                    success: false,
                    message: 'Invalid credentials'
                });
            }
    
            if (password !== admin.password) {
                return res.status(401).json({
                    success: false,
                    message: 'Invalid credentials'
                });
            }
    
            const token = jwt.sign(
                { 
                    id: admin.id,
                    email: admin.email,
                    role: 'administrator'
                },
                process.env.JWT_SECRET || 'your-default-secret-key',
                { expiresIn: '24h' }
            );
    
            // Send the redirect URL along with the token and admin details
            res.status(200).json({
                success: true,
                message: 'Login successful',
                data: {
                    token,
                    admin: {
                        id: admin.id,
                        email: admin.email,
                    },
                    redirect: 'http://localhost:5173/admin/dashboard'  // Redirect URL
                }
            });
    
        } catch (error) {
            console.error('Login error:', error);
            res.status(500).json({
                success: false,
                message: 'An error occurred during login'
            });
        }
    });
    

const PORT = process.env.PORT || 8000;
app.listen(PORT, () => console.log(`Server running on port ${PORT}`));
