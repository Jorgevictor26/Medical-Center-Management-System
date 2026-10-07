import { Component, OnInit, inject, signal } from '@angular/core';

import { DashboardService } from '../../services/dashboard.service';
import { Dashboard as DashboardModel } from '../../models/dashboard.model';

@Component({
  selector: 'app-dashboard',
  imports: [],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.css',
})
export class Dashboard implements OnInit {
  private dashboardService = inject(DashboardService);

  dashboard = signal<DashboardModel | null>(null);
  loading = signal(true);
  error = signal('');

  ngOnInit(): void {
    this.loadDashboard();
  }

  loadDashboard(): void {
    this.loading.set(true);
    this.error.set('');

    this.dashboardService.getDashboard().subscribe({
      next: (data) => {
        this.dashboard.set(data);
        this.loading.set(false);
      },
      error: (err) => {
        console.error(err);
        this.error.set('Unable to load dashboard.');
        this.loading.set(false);
      },
    });
  }
}