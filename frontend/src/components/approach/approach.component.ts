import { Component, ChangeDetectionStrategy } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-approach',
  standalone: true,
  imports: [CommonModule],
  template: `
    <section class="approach section-padding" id="approach">
      <div class="container">
        <h2 class="section-title">Как я работаю</h2>

        <div class="approach-grid">
          <div class="approach-card">
            <div class="icon">🎯</div>
            <h3>Подход к задачам</h3>
            <ul>
              <li>Анализирую требования перед началом разработки</li>
              <li>Проектирую архитектуру с учетом масштабируемости</li>
              <li>Пишу чистый, поддерживаемый код с комментариями</li>
              <li>Провожу код-ревью и внедряю лучшие практики</li>
              <li>Работаю как с legacy-кодом, так и с новыми проектами</li>
            </ul>
          </div>

          <div class="approach-card">
            <div class="icon">🤖</div>
            <h3>AI в моей работе</h3>
            <ul>
              <li>Использую AI для быстрого прототипирования UI</li>
              <li>Генерирую тесты и документацию через AI</li>
              <li>Оптимизирую код с помощью AI-инструментов</li>
              <li>Интегрирую AI API в коммерческие проекты</li>
              <li>Использую Cursor и GitHub Copilot ежедневно</li>
            </ul>
          </div>

          <div class="approach-card">
            <div class="icon">💡</div>
            <h3>Качество кода</h3>
            <ul>
              <li>Делаю ставку на качество и стандартизацию</li>
              <li>Покрываю код юнит-тестами (Jest, Jasmine)</li>
              <li>Настраиваю CI/CD (GitLab CI, GitHub Actions)</li>
              <li>Соблюдаю ESLint, Prettier, Stylelint</li>
              <li>Документирую сложные моменты</li>
            </ul>
          </div>
        </div>
      </div>
    </section>
  `,
  changeDetection: ChangeDetectionStrategy.Eager,
  styleUrls: ['./approach.component.scss']
})
export class ApproachComponent {}
