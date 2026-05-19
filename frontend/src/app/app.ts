import { Component, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HeaderComponent } from '../components/header/header.component';
import { AboutComponent } from '../components/about/about.component';
import { ApproachComponent } from '../components/approach/approach.component';
import { CasesComponent } from '../components/cases/cases.component';
import { ContactsComponent } from '../components/contacts/contacts.component';
import { FooterComponent } from '../components/footer/footer.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    CommonModule,
    HeaderComponent,
    AboutComponent,
    ApproachComponent,
    CasesComponent,
    ContactsComponent,
    FooterComponent,
    ContactsComponent
  ],
  template: `
    <app-header />
    <main>
      <app-about />
      <app-approach />
      <app-cases />
      <app-contacts />
    </main>
    <app-footer />
  `,
  styles: [`
    main {
      overflow-x: hidden;
    }
  `]
})
export class AppComponent {
  title = signal('Андрей Ефремцев - Frontend разработчик');
}
