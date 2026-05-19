// Заглушка для AI-подсказки (без реального API)
// При наличии OPENAI_API_KEY в .env этот файл можно заменить на реальную интеграцию

export async function getAiSuggestion(userText) {
  // Имитируем задержку ответа (как у реального AI)
  await new Promise(resolve => setTimeout(resolve, 600));

  // Простая логика "улучшения" текста для демонстрации
  let suggestion = userText.trim();

  // Если текст слишком короткий
  if (suggestion.length < 20) {
    suggestion = suggestion +
      " Буду благодарен за обратную связь!";
  }

  // Если в тексте нет обращения
  if (!suggestion.match(/здравствуй|привет|добрый день/gi)) {
    suggestion = "Здравствуйте! " + suggestion.charAt(0).toLowerCase() + suggestion.slice(1);
  }

  // Если текст заканчивается точкой
  if (!suggestion.endsWith('.') && !suggestion.endsWith('!') && !suggestion.endsWith('?')) {
    suggestion += '.';
  }

  // Добавляем пояснение, что это демо-режим
  suggestion += "\n\n(🤖 Демо-режим AI: в боевой версии здесь будет реальное улучшение текста через OpenAI)";

  return suggestion;
}
