import { Injectable } from '@angular/core';

export interface ContactData {
  name: string;
  phone: string;
  email: string;
  comment: string;
}

@Injectable({
  providedIn: 'root'
})
export class ContactService {
  async sendMessage(data: ContactData): Promise<any> {
    const response = await fetch('/api/send-message', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data)
    });

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.error || 'Ошибка отправки');
    }

    return response.json();
  }
}
