# PTC Alumni Tracking System

A modern, multi-role web platform designed to connect **graduates, colleges, companies, and administrators** in one centralized ecosystem.  
The system helps manage alumni engagement, job opportunities, workshops, scholarships, notifications, support requests, and institutional follow-up through a professional academic interface.

---

## Overview

The **PTC Alumni Tracking System** was developed to provide Palestine Technical College with a practical and scalable solution for alumni affairs management.  
It addresses the gap between graduates, the college, and employers by offering a structured digital platform that supports communication, opportunity sharing, and long-term alumni tracking.

This project simulates a real institutional environment with separate dashboards, realistic workflows, approval processes, notifications, and role-based permissions.

---

## Key Features

- Multi-role authentication system
- Separate dashboard for each user role
- Alumni profile and opportunity tracking
- Job posting and application workflow
- Workshop publishing and registration
- Scholarships management
- Recommendations and leaderboard
- Real-time notification workflow
- Support request and tracking system
- Admin system settings management
- Arabic and English language support
- Dark and light mode support
- Role-based dashboard themes
- Responsive and modern UI

---

## User Roles

### Alumni
Alumni users can:

- Sign in and manage their profile
- Browse available job opportunities
- Apply for jobs
- Register for workshops
- View and apply for scholarships
- Receive recommendations
- Track submitted applications
- View leaderboard rankings
- Receive notifications

### College
College users can:

- Manage alumni records
- Publish and manage college workshops
- Publish and manage scholarships
- Publish college job opportunities
- Review company-submitted jobs and workshops
- Approve or reject content before publishing
- View job applicants and workshop registrations
- Publish announcements
- Access reports

### Company
Company users can:

- Register and access company dashboard
- Publish job opportunities
- Propose workshops
- Browse alumni
- Review received applications
- Receive approval or rejection notifications from the college

### Admin
Admin users can:

- Manage users
- Manage platform content
- Access reports
- Update system settings
- Change institution name
- Customize admin/public primary color
- Track last backup status
- Access support center features

---

## Workflow Logic

The system includes a realistic academic workflow:

- **Company-submitted jobs and workshops** require **college approval**
- **College-created jobs and workshops** can be managed directly by the college
- **Alumni only see approved content**
- **Notifications** are triggered when:
  - a company submits new content
  - the college approves or rejects content
  - new opportunities become available to alumni

---

## UI / UX Highlights

- Clean academic interface
- Distinct login color theme for each role
- Dedicated color identity for each dashboard
- Admin-controlled color reflected in:
  - Admin dashboard
  - Public homepage
- Responsive layout for desktop and mobile
- Consistent visual hierarchy and modern styling

---

## Technology Stack

### Backend
- Laravel
- PHP
- MySQL

### Frontend
- Blade Templates
- Tailwind CSS
- JavaScript
- Vite
- Lucide Icons

### Development Environment
- WAMP Server
- Mailtrap for email testing

---

## Project Structure

```bash
app/
 ├── Http/Controllers
 ├── Models
database/
 ├── migrations
 ├── seeders
resources/
 ├── views
 ├── js
 ├── css
routes/
 ├── web.php
public/
