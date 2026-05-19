import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Путь к лог-файлу (создастся в папке backend)
const logFilePath = path.join(__dirname, '..', 'messages.log');

export async function sendEmails({ name, phone, email, comment }) {
  // Формируем запись для лога
  const logEntry = `
┌─────────────────────────────────────────────
│ 📬 НОВОЕ СООБЩЕНИЕ
│ 🕐 Время: ${new Date().toLocaleString('ru-RU')}
│ 👤 Имя: ${name}
│ 📞 Телефон: ${phone}
│ ✉️ Email: ${email}
│ 💬 Комментарий:
│   ${comment.replace(/\n/g, '\n│   ')}
└─────────────────────────────────────────────
`;

  // Записываем в файл (создастся автоматически)
  fs.appendFileSync(logFilePath, logEntry, 'utf8');

  // Логируем в консоль для отладки
  console.log(`✅ Сообщение от ${name} сохранено в ${logFilePath}`);

  // Возвращаем успешный результат (имитируем отправку)
  return {
    success: true,
    message: 'Сообщение сохранено в лог-файл'
  };
}
