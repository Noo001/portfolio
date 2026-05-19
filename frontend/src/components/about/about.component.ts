import { Component, signal } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-about',
  standalone: true,
  imports: [CommonModule],
  template: `
    <section class="about section-padding" id="about">
      <div class="container">
        <h2 class="section-title">Обо мне</h2>

        <div class="about-grid">
          <div class="about-info fade-in-up">
            <h3>Андрей Ефремцев</h3>
            <p class="lead">Frontend-разработчик с 8+ годами опыта</p>
            <p>Глубокая экспертиза в TypeScript, архитектуре высоконагруженных приложений,
              дизайн-системах и микрофронтендах. Основной стек — Angular, но благодаря сильной базе
              быстро осваиваю React и Vue и готов применять их в коммерческой разработке.</p>

            <div class="experience-badge">
              <span class="years">8 лет</span>
              <span class="text">коммерческой разработки</span>
            </div>

            <div class="highlights">
              <div class="highlight-item">
                <strong>📚 EdTech</strong> — лидировал разработку проекта с миллионами пользователей
              </div>
              <div class="highlight-item">
                <strong>🏦 Финтех</strong> — разрабатывал приложения для ВСК и SBI Банка
              </div>
              <div class="highlight-item">
                <strong>🏛️ Госсектор</strong> — участвовал в проектах для Минпромторга
              </div>
            </div>
          </div>

          <div class="tech-stack fade-in-up">
            <h4>Основной стек</h4>
            <div class="tech-items">
              @for (tech of mainStack(); track tech) {
                <span class="tech-tag expert">{{ tech }}</span>
              }
            </div>

            <h4>Дополнительно</h4>
            <div class="tech-items">
              @for (tech of additionalStack(); track tech) {
                <span class="tech-tag advanced">{{ tech }}</span>
              }
            </div>

            <h4>Инструменты</h4>
            <div class="tech-items">
              @for (tool of tools(); track tool) {
                <span class="tech-tag">{{ tool }}</span>
              }
            </div>
          </div>
        </div>
      </div>
    </section>
  `,
  styleUrls: ['./about.component.scss']
})
export class AboutComponent {
  mainStack = signal(['Angular', 'TypeScript', 'RxJS', 'NgRx', 'Signals']);
  additionalStack = signal(['React', 'Vue', 'Node.js', 'Express']);
  tools = signal(['Git', 'Webpack', 'Jest', 'ESLint', 'Prettier', 'Figma', 'Jira']);
}
