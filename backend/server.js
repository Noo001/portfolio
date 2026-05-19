import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import { sendEmails } from './services/emailService.js';
import { getAiSuggestion } from './services/aiService.js';

dotenv.config();

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());

// Эндпоинт для отправки сообщений
app.post('/api/send-message', async (req, res) => {
  const { name, phone, email, comment } = req.body;

  // Валидация
  if (!name || !phone || !email || !comment) {
    return res.status(400).json({ error: 'Все поля обязательны для заполнения' });
  }

  // Валидация email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    return res.status(400).json({ error: 'Некорректный email адрес' });
  }

  try {
    await sendEmails({ name, phone, email, comment });
    res.json({ success: true, message: 'Письма успешно отправлены' });
  } catch (error) {
    console.error('Ошибка отправки писем:', error);
    res.status(500).json({ error: 'Ошибка при отправке писем. Попробуйте позже.' });
  }
});

// Эндпоинт для AI-подсказки
app.post('/api/ai-suggest', async (req, res) => {
  const { text } = req.body;

  if (!text || text.trim().length === 0) {
    return res.status(400).json({ error: 'Текст не предоставлен' });
  }

  try {
    const suggestion = await getAiSuggestion(text);
    res.json({ suggestion });
  } catch (error) {
    console.error('Ошибка AI:', error);
    res.status(500).json({ error: 'AI сервис временно недоступен' });
  }
});

app.listen(PORT, () => {
  console.log(`🚀 Сервер запущен на http://localhost:${PORT}`);
});
