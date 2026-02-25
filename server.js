const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');
require('dotenv').config();

const app = express();
app.use(cors());
app.use(express.json());

const dbConfig = {
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'miumc_db'
};

const pool = mysql.createPool(dbConfig);

// =========================================================================
// RUTAS DE APOYO (CARRERAS Y MENCIONES)
// =========================================================================

app.get('/api/careers', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM careers ORDER BY id ASC');
        res.json(rows);
    } catch (error) { res.status(500).json({ error: error.message }); }
});

app.get('/api/specializations/:careerId', async (req, res) => {
    try {
        const { careerId } = req.params;
        const [rows] = await pool.query('SELECT * FROM specializations WHERE career_id = ?', [careerId]);
        res.json(rows);
    } catch (error) { res.status(500).json({ error: error.message }); }
});

app.get('/api/subjects/:specializationId', async (req, res) => {
    try {
        const { specializationId } = req.params;
        const [rows] = await pool.query(
            'SELECT * FROM subjects WHERE specialization_id IS NULL OR specialization_id = ? ORDER BY semester ASC',
            [specializationId]
        );
        res.json(rows);
    } catch (error) { res.status(500).json({ error: error.message }); }
});

app.get('/api/progress/:studentCode', async (req, res) => {
    try {
        const { studentCode } = req.params;
        const query = `
            SELECT s.code 
            FROM academic_records ar
            JOIN users u ON ar.user_id = u.id
            JOIN subjects s ON ar.subject_id = s.id
            WHERE u.student_code = ? AND ar.status = 'aprobada'
        `;
        const [rows] = await pool.query(query, [studentCode]);
        const codes = rows.map(r => r.code);
        res.json(codes);
    } catch (error) { res.status(500).json({ error: error.message }); }
});

// =========================================================================
// RUTAS DE GESTIÓN DE INSCRIPCIONES
// =========================================================================

// Recuperar inscripción guardada
app.get('/api/enrollments/:studentCode', async (req, res) => {
    try {
        const { studentCode } = req.params;
        const query = `
            SELECT s.*, e.schedule_data 
            FROM enrollments e
            JOIN users u ON e.user_id = u.id
            JOIN subjects s ON e.subject_id = s.id
            WHERE u.student_code = ? AND e.period = '2026-I'
        `;
        const [rows] = await pool.query(query, [studentCode]);

        // Reconstruimos el array combinando los datos de la materia y el JSON del horario
        const enrollments = rows.map(row => ({
            ...row,
            ...row.schedule_data
        }));
        res.json(enrollments);
    } catch (error) { res.status(500).json({ error: error.message }); }
});

// Guardar nueva inscripción
app.post('/api/enrollments/save', async (req, res) => {
    const { studentCode, period = '2026-I', enrolledSubjects } = req.body;
    const connection = await pool.getConnection();
    try {
        await connection.beginTransaction();

        const [users] = await connection.query('SELECT id FROM users WHERE student_code = ?', [studentCode]);
        if (users.length === 0) throw new Error("Usuario no encontrado");
        const userId = users[0].id;

        // Limpiamos la inscripción anterior de ese periodo
        await connection.query('DELETE FROM enrollments WHERE user_id = ? AND period = ?', [userId, period]);

        // Insertamos las nuevas materias
        if (enrolledSubjects && enrolledSubjects.length > 0) {
            const placeholders = enrolledSubjects.map(() => '?').join(',');
            const codes = enrolledSubjects.map(s => s.codigo);
            const [subjects] = await connection.query(`SELECT id, code FROM subjects WHERE code IN (${placeholders})`, codes);

            const insertValues = enrolledSubjects.map(inscrita => {
                const subject = subjects.find(s => s.code === inscrita.codigo);
                const scheduleData = JSON.stringify({
                    day: inscrita.day,
                    dayIdx: inscrita.dayIdx,
                    startTime: inscrita.startTime,
                    endTime: inscrita.endTime,
                    room: inscrita.room,
                    color: inscrita.color,
                    duration: inscrita.duration,
                    professor: inscrita.professor
                });
                return [userId, subject.id, period, scheduleData];
            });

            await connection.query('INSERT INTO enrollments (user_id, subject_id, period, schedule_data) VALUES ?', [insertValues]);
        }

        await connection.commit();
        res.json({ success: true, message: 'Inscripción guardada correctamente' });
    } catch (error) {
        await connection.rollback();
        res.status(500).json({ success: false, error: error.message });
    } finally {
        connection.release();
    }
});

// =========================================================================
// RUTAS DE AUTENTICACIÓN Y ONBOARDING
// =========================================================================

app.post('/api/auth/login', async (req, res) => {
    const { studentCode, password } = req.body;
    try {
        const query = `
            SELECT u.*, s.name as mencion_name, c.name as career_name 
            FROM users u
            LEFT JOIN specializations s ON u.specialization_id = s.id
            LEFT JOIN careers c ON u.career_id = c.id
            WHERE u.student_code = ?
        `;
        const [users] = await pool.query(query, [studentCode]);
        if (users.length === 0) return res.status(401).json({ success: false, message: 'Usuario no encontrado' });

        if (users[0].password_hash !== password) {
            return res.status(401).json({ success: false, message: 'Contraseña incorrecta' });
        }
        res.json({ success: true, user: users[0] });
    } catch (error) { res.status(500).json({ success: false, error: error.message }); }
});

app.post('/api/auth/register', async (req, res) => {
    const { studentCode, email, password } = req.body;
    try {
        const query = `INSERT INTO users (student_code, email, password_hash, full_name, role) VALUES (?, ?, ?, '', 'cadete')`;
        await pool.query(query, [studentCode, email, password]);
        res.json({ success: true });
    } catch (error) { res.status(500).json({ success: false, message: error.message }); }
});

app.post('/api/onboarding/complete', async (req, res) => {
    const { studentCode, fullName, age, birthDate, phone, careerId, mencionId, approvedSubjects } = req.body;
    try {
        // Actualizar perfil del usuario con TELÉFONO y CARRERA
        await pool.query(
            'UPDATE users SET full_name = ?, age = ?, birth_date = ?, phone = ?, career_id = ?, specialization_id = ? WHERE student_code = ?',
            [fullName, age, birthDate, phone || 'Sin teléfono', careerId || 1, mencionId || 1, studentCode]
        );

        const [users] = await pool.query('SELECT id FROM users WHERE student_code = ?', [studentCode]);
        const userId = users[0].id;

        if (approvedSubjects && approvedSubjects.length > 0) {
            const placeholders = approvedSubjects.map(() => '?').join(',');
            const [subjects] = await pool.query(`SELECT id FROM subjects WHERE code IN (${placeholders})`, approvedSubjects);

            if (subjects.length > 0) {
                const insertValues = subjects.map(s => [userId, s.id, 'aprobada']);
                await pool.query('INSERT IGNORE INTO academic_records (user_id, subject_id, status) VALUES ?', [insertValues]);
            }
        }
        res.json({ success: true });
    } catch (error) { res.status(500).json({ success: false, error: error.message }); }
});

// =========================================================================
// RUTAS ADMINISTRATIVAS (PANEL DE CONTROL)
// =========================================================================

// Listado global de usuarios: TRAEMOS u.* PARA QUE NO FALTE EL TELÉFONO NI LOS IDs
app.get('/api/admin/users', async (req, res) => {
    try {
        const query = `
            SELECT 
                u.*, 
                c.name as career_name, 
                s.name as mencion_name 
            FROM users u
            LEFT JOIN careers c ON u.career_id = c.id
            LEFT JOIN specializations s ON u.specialization_id = s.id
            ORDER BY u.role ASC, u.full_name ASC
        `;
        const [rows] = await pool.query(query);
        res.json(rows);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Editar datos de usuario: INCLUIDO EL TELÉFONO (PHONE) Y LA ESPECIALIZACIÓN
app.put('/api/admin/users/:id', async (req, res) => {
    const { id } = req.params;
    const { full_name, email, phone, role, career_id, specialization_id } = req.body;
    try {
        const query = `
            UPDATE users 
            SET full_name = ?, email = ?, phone = ?, role = ?, career_id = ?, specialization_id = ?
            WHERE id = ?
        `;
        await pool.query(query, [full_name, email, phone, role, career_id || 1, specialization_id || 1, id]);
        res.json({ success: true, message: 'Usuario actualizado correctamente' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.delete('/api/admin/users/:id', async (req, res) => {
    const { id } = req.params;
    try {
        await pool.query('DELETE FROM users WHERE id = ?', [id]);
        res.json({ success: true, message: 'Usuario eliminado correctamente' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Actualización masiva de récord académico
app.post('/api/admin/update-records-bulk', async (req, res) => {
    const { userId, approvedSubjectCodes } = req.body;
    const connection = await pool.getConnection();
    try {
        await connection.beginTransaction();

        await connection.query('DELETE FROM academic_records WHERE user_id = ?', [userId]);

        if (approvedSubjectCodes && approvedSubjectCodes.length > 0) {
            const placeholders = approvedSubjectCodes.map(() => '?').join(',');
            const [subjects] = await connection.query(`SELECT id FROM subjects WHERE code IN (${placeholders})`, approvedSubjectCodes);

            if (subjects.length > 0) {
                const insertValues = subjects.map(s => [userId, s.id, 'aprobada']);
                await connection.query('INSERT INTO academic_records (user_id, subject_id, status) VALUES ?', [insertValues]);
            }
        }

        await connection.commit();
        res.json({ success: true, message: 'Récord actualizado con éxito' });
    } catch (error) {
        await connection.rollback();
        console.error("Error en bulk update:", error);
        res.status(500).json({ success: false, error: error.message });
    } finally {
        connection.release();
    }
});

const PORT = 5000;
app.listen(PORT, () => {
    console.log(`🚀 Servidor MiUMC corriendo en http://localhost:${PORT}`);
});