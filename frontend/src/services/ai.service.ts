import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class AiService {
  async getSuggestion(text: string): Promise<string> {
    const response = await fetch('/api/ai-suggest', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ text })
    });

    if (!response.ok) {
      throw new Error('AI service unavailable');
    }

    const data = await response.json();
    return data.suggestion;
  }
}
