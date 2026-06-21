import { Component, ChangeDetectionStrategy } from '@angular/core';

@Component({
  selector: 'app-footer',
  standalone: true,
  template: `
    <footer>
      <div class="container">
        <p>&copy; 2026 Андрей Ефремцев. Все права защищены.</p>
        <p>Frontend-разработчик | Angular эксперт | 8+ лет опыта</p>
      </div>
    </footer>
  `,
  changeDetection: ChangeDetectionStrategy.Eager,
  styleUrls: ['./footer.component.scss']
})
export class FooterComponent {}
