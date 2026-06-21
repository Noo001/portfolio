import { Component, signal, HostListener, ChangeDetectionStrategy } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [CommonModule],
  template: `
    <header [class.scrolled]="isScrolled()">
      <div class="container">
        <nav>
          <div class="logo">
            <a href="#" (click)="scrollToTop($event)">
              <span class="name">Андрей </span>
              <span class="highlight">Ефремцев</span>
            </a>
          </div>

          <button class="mobile-menu-btn" (click)="toggleMobileMenu()" [class.active]="mobileMenuOpen()">
            <span></span>
            <span></span>
            <span></span>
          </button>

          <ul class="nav-links" [class.open]="mobileMenuOpen()">
            <li><a href="#about" (click)="closeMobileMenu()">Обо мне</a></li>
            <li><a href="#approach" (click)="closeMobileMenu()">Подход</a></li>
            <li><a href="#cases" (click)="closeMobileMenu()">Проекты</a></li>
            <li><a href="#contacts" (click)="closeMobileMenu()">Контакты</a></li>
          </ul>
        </nav>
      </div>
    </header>
  `,
  changeDetection: ChangeDetectionStrategy.Eager,
  styleUrls: ['./header.component.scss']
})
export class HeaderComponent {
  isScrolled = signal(false);
  mobileMenuOpen = signal(false);

  @HostListener('window:scroll')
  onScroll() {
    this.isScrolled.set(window.scrollY > 50);
  }

  toggleMobileMenu() {
    this.mobileMenuOpen.update(v => !v);
  }

  closeMobileMenu() {
    this.mobileMenuOpen.set(false);
  }

  scrollToTop(event: Event) {
    event.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
    this.closeMobileMenu();
  }
}
