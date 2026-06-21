import { Component, signal, ChangeDetectionStrategy } from '@angular/core';
import { CommonModule } from '@angular/common';

interface Case {
  id: number;
  title: string;
  company: string;
  period: string;
  description: string;
  achievements: string[];
  techStack: string[];
}

@Component({
  selector: 'app-cases',
  standalone: true,
  imports: [CommonModule],
  template: `
    <section class="cases section-padding" id="cases">
      <div class="container">
        <h2 class="section-title">Ключевые проекты</h2>

        <div class="cases-grid">
          @for (case of cases(); track case.id) {
            <div class="case-card fade-in-up">
              <div class="case-header">
                <h3>{{ case.title }}</h3>
                <p class="company">{{ case.company }}</p>
                <p class="period">{{ case.period }}</p>
              </div>

              <div class="case-body">
                <p>{{ case.description }}</p>

                <div class="achievements">
                  <h4>Мои достижения:</h4>
                  <ul>
                    @for (achievement of case.achievements; track achievement) {
                      <li>{{ achievement }}</li>
                    }
                  </ul>
                </div>

                <div class="tech-used">
                  @for (tech of case.techStack; track tech) {
                    <span class="tech-badge">{{ tech }}</span>
                  }
                </div>
              </div>
            </div>
          }
        </div>
      </div>
    </section>
  `,
  changeDetection: ChangeDetectionStrategy.Eager,
  styleUrls: ['./cases.component.scss']
})
export class CasesComponent {
  cases = signal<Case[]>([
    {
      id: 1,
      title: 'Электронные рабочие тетради',
      company: 'Издательство "Просвещение"',
      period: 'Май 2023 — Декабрь 2025',
      description: 'Крупнейший образовательный проект с миллионной аудиторией. Высоконагруженное SPA со сложной бизнес-логикой.',
      achievements: [
        'Спроектировал архитектуру высоконагруженного приложения',
        'Разработал переиспользуемые компоненты и модули',
        'Обучил команду Angular практикам',
        'Оптимизировал производительность до 95+ в Lighthouse'
      ],
      techStack: ['Angular', 'TypeScript', 'RxJS', 'NgRx', 'WebSocket']
    },
    {
      id: 2,
      title: 'Страховые приложения',
      company: 'ВСК, САО',
      period: 'Август 2021 — Март 2023',
      description: 'Разработка фронтенда для страховых приложений с высокими требованиями к надёжности.',
      achievements: [
        'Создал и провёл корпоративные курсы по Angular для 15+ разработчиков',
        'Внедрил дизайн-систему и UI-кит',
        'Настроил CI/CD процессы',
        'Проводил код-ревью и обучал команду'
      ],
      techStack: ['Angular', 'TypeScript', 'REST API', 'SCSS', 'Jest']
    },
    {
      id: 3,
      title: 'Госпроект для Минпромторга',
      company: 'Галактика',
      period: 'Август 2019 — Ноябрь 2020',
      description: 'Крупный государственный проект с командой 20+ человек.',
      achievements: [
        'Реализовал 70% фронтенд-функционала системы',
        'Настроил взаимодействие с PostgreSQL через хранимые процедуры',
        'Самостоятельно освоил DevExpress для проекта'
      ],
      techStack: ['JavaScript', 'DevExpress', 'PostgreSQL', 'jQuery']
    }
  ]);
}
