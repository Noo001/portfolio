import { Component, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ContactService } from '../../services/contact.service';
import { AiService } from '../../services/ai.service';

interface ContactForm {
  name: string;
  phone: string;
  email: string;
  comment: string;
}

@Component({
  selector: 'app-contacts',
  standalone: true,
  imports: [CommonModule, FormsModule],
  template: `
    <section class="contacts section-padding" id="contacts">
      <div class="container">
        <h2 class="section-title">Свяжитесь со мной</h2>

        <div class="contacts-grid">
          <div class="contact-info fade-in-up">
            <h3>Контакты</h3>
            <div class="contact-details">
              <div class="contact-item">
                <span class="label">📧 Email:</span>
                <a href="mailto:noo_@bk.ru">noo_@bk.ru</a>
              </div>
              <div class="contact-item">
                <span class="label">📱 Телефон:</span>
                <a href="tel:+79043039420">+7 (904) 303-94-20</a>
              </div>
              <div class="contact-item">
                <span class="label">💬 Telegram:</span>
                <a href="https://t.me/pompadurik" target="_blank">@pompadurik</a>
              </div>
              <div class="contact-item">
                <span class="label">📍 Город:</span>
                <span>Воронеж</span>
              </div>
            </div>

            <div class="availability">
              <h4>Готовность к работе</h4>
              <p>✅ Полная занятость<br>
              ✅ Удаленная работа<br>
              ✅ Гибридный формат<br>
              ✅ Командировки</p>
            </div>
          </div>

          <div class="contact-form fade-in-up">
            <form #contactForm="ngForm" (ngSubmit)="onSubmit()">
              <div class="form-group">
                <input
                  type="text"
                  [(ngModel)]="formData.name"
                  name="name"
                  placeholder="Ваше имя *"
                  required
                  #name="ngModel"
                >
                @if (name.invalid && name.touched) {
                  <small class="error">Имя обязательно</small>
                }
              </div>

              <div class="form-group">
                <input
                  type="tel"
                  [(ngModel)]="formData.phone"
                  name="phone"
                  placeholder="Телефон *"
                  required
                  #phone="ngModel"
                >
                @if (phone.invalid && phone.touched) {
                  <small class="error">Телефон обязателен</small>
                }
              </div>

              <div class="form-group">
                <input
                  type="email"
                  [(ngModel)]="formData.email"
                  name="email"
                  placeholder="Email *"
                  required
                  email
                  #email="ngModel"
                >
                @if (email.invalid && email.touched) {
                  <small class="error">Введите корректный email</small>
                }
              </div>

              <div class="form-group">
                <textarea
                  [(ngModel)]="formData.comment"
                  name="comment"
                  placeholder="Комментарий *"
                  rows="4"
                  required
                  #comment="ngModel"
                ></textarea>
                @if (comment.invalid && comment.touched) {
                  <small class="error">Комментарий обязателен</small>
                }
              </div>

              <div class="form-actions">
                <button
                  type="button"
                  class="btn-ai"
                  (click)="getAiSuggestion()"
                  [disabled]="isAiLoading()">
                  {{ isAiLoading() ? '🤔 Думаю...' : '✨ AI-подсказка' }}
                </button>

                <button
                  type="submit"
                  class="btn-submit"
                  [disabled]="contactForm.invalid || isLoading()">
                  {{ isLoading() ? 'Отправка...' : 'Отправить' }}
                </button>
              </div>

              @if (statusMessage()) {
                <div class="status-message" [class.error]="statusType() === 'error'">
                  {{ statusMessage() }}
                </div>
              }
            </form>
          </div>
        </div>
      </div>
    </section>
  `,
  styleUrls: ['./contacts.component.scss']
})
export class ContactsComponent {
  formData: ContactForm = {
    name: '',
    phone: '',
    email: '',
    comment: ''
  };

  isLoading = signal(false);
  isAiLoading = signal(false);
  statusMessage = signal('');
  statusType = signal<'success' | 'error' | ''>('');

  constructor(
    private contactService: ContactService,
    private aiService: AiService
  ) {}

  async onSubmit() {
    this.isLoading.set(true);
    this.statusMessage.set('');

    try {
      const result = await this.contactService.sendMessage(this.formData);
      this.statusMessage.set('✅ Сообщение отправлено! Копия придет вам на почту.');
      this.statusType.set('success');
      this.resetForm();
    } catch (error: any) {
      this.statusMessage.set(`❌ Ошибка: ${error.message || 'Не удалось отправить'}`);
      this.statusType.set('error');
    } finally {
      this.isLoading.set(false);
      setTimeout(() => {
        this.statusMessage.set('');
        this.statusType.set('');
      }, 5000);
    }
  }

  async getAiSuggestion() {
    if (!this.formData.comment.trim()) {
      this.statusMessage.set('✏️ Напишите начало комментария для AI-подсказки');
      this.statusType.set('error');
      setTimeout(() => this.statusMessage.set(''), 3000);
      return;
    }

    this.isAiLoading.set(true);

    try {
      const suggestion = await this.aiService.getSuggestion(this.formData.comment);
      this.formData.comment = suggestion;
      this.statusMessage.set('✨ AI улучшил ваш комментарий');
      this.statusType.set('success');
      setTimeout(() => this.statusMessage.set(''), 2000);
    } catch (error) {
      this.statusMessage.set('⚠️ AI временно недоступен');
      this.statusType.set('error');
      setTimeout(() => this.statusMessage.set(''), 3000);
    } finally {
      this.isAiLoading.set(false);
    }
  }

  private resetForm() {
    this.formData = {
      name: '',
      phone: '',
      email: '',
      comment: ''
    };
  }
}
