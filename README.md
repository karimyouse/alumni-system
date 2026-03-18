# PTC Alumni Tracking System

A web-based platform developed to strengthen communication and engagement between the college, alumni, companies, and system administrators. The system provides a centralized environment for managing alumni profiles, job opportunities, workshops, scholarships, announcements, recommendations, notifications, reports, and support requests.

## Project Overview

The **PTC Alumni Tracking System** was designed to address the need for a structured digital platform that connects graduates with their academic institution and the labor market. It supports multiple user roles and provides each role with dedicated features and dashboards.

The system improves alumni follow-up, facilitates opportunity sharing, supports institutional reporting, and enhances collaboration between the college and partner companies.

## Main Objectives

- Build a centralized alumni management system
- Improve communication between alumni, college, and companies
- Enable companies to publish job opportunities and workshops
- Allow the college to manage content and monitor alumni engagement
- Provide administrators with control over system settings, users, and reports
- Offer a professional, modern, and user-friendly interface

## User Roles

### Alumni
Alumni users can:
- Sign in and manage their profiles
- Browse and apply for job opportunities
- Register for workshops
- View available scholarships
- Receive recommendations
- Track their applications
- View leaderboard rankings
- Receive notifications

### College
College users can:
- Manage alumni data
- Publish and manage workshops
- Publish and manage scholarships
- Post college job opportunities
- Review and approve or reject company-submitted jobs and workshops
- View applicants and registrations
- Publish announcements
- View reports

### Company
Company users can:
- Register and log in
- Post job opportunities
- Propose workshops
- Browse alumni
- Review applications
- Receive notifications when content is approved or rejected

### Admin / Super Admin
Admin users can:
- Manage users
- Manage content
- Review reports
- Manage system settings
- Update institution name
- Change the public/admin primary color
- Track backup status
- Access support center features

## Core Features

- Multi-role authentication system
- Public homepage with system overview
- Separate dashboard for each role
- Alumni profile management
- Job posting and application workflow
- Workshop creation and registration
- Scholarship publishing and application
- Notifications system
- Recommendations feature
- Leaderboard
- Support request and ticket tracking
- Admin settings panel
- Role-based theme styling
- Arabic and English language support
- Dark and light mode support

## Approval Workflow

The system includes a structured approval workflow:

- **Company jobs and workshops** require college review before becoming visible to alumni
- **College jobs and workshops** are published directly by the college
- The college can:
  - approve or reject company submissions
  - view applicants/registrations
  - edit and delete only college-owned content

## Theme and UI Logic

The platform uses a modern academic design with:

- Role-based dashboard themes
- Dedicated role colors in the login form
- Admin color customization through system settings
- Public homepage color linked to the admin primary color setting
- Responsive layout for desktop and mobile screens

## Technologies Used

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

### Other Tools
- Mailtrap for email testing
- WAMP Server for local development

## Project Structure

The project is organized using Laravel’s MVC architecture:

- `app/Http/Controllers` → Controllers
- `app/Models` → Models
- `resources/views` → Blade views
- `routes` → Web routes
- `database/migrations` → Database schema
- `database/seeders` → Sample and system data

## Installation Guide

### 1. Clone the project
```bash
git clone <your-repository-url>
cd alumni-tracking-system
