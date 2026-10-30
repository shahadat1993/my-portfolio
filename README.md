# 🚀 Full-Stack Developer Portfolio & CMS

A high-performance, modern full-stack web application designed to showcase projects, technical skills, and interactive experiences. Built with a robust **Laravel REST API backend** and a visually immersive **React (Vite) frontend** featuring advanced GSAP animation controls and responsive layout architecture.

![Portfolio Banner](https://img.shields.io/badge/Stack-Laravel%20%7C%20React%20%7C%20GSAP-blue)
![License](https://img.shields.io/badge/License-MIT-green)
![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen)

---

## ✨ Key Features & Highlights

### 🎨 Frontend (Client Interface)
* **Smooth Animations & Interactive UI:** Powered by **GSAP (ScrollTrigger, MotionPath, Flip)** and **Lenis** for ultra-smooth scrolling performance.
* **Modern Component Architecture:** Built using React functional components, hooks, and scalable CSS modular styling.
* **Global State & Preferences:** Dynamic context management for dark/light themes, smooth scrolling options, and interactive cursor effects (`PointerFX`).
* **Dynamic Search & Filtering:** Client-side live search overlay for instant project and blog discovery.
* **Fully Responsive:** Optimized for desktop, tablet, and mobile displays with crisp typography and dynamic layout adjustments.

### 🛠️ Backend (RESTful API & CMS)
* **REST API Endpoints:** Clean controller-driven API routes in Laravel providing dynamic project, blog, and contact form handling.
* **Secure Authentication:** User and session management with environment-driven security key configurations.
* **Database & Migrations:** Structured database schema using Laravel migrations and data seeders for quick deployment.
* **Asset & Storage Management:** Automated seed asset generation scripts and storage link configurations for portfolio media.

---

## 🏗️ Tech Stack & Architecture

### **Frontend**
* **Framework:** React 18+ (Vite)
* **State & Routing:** React Router DOM v6, React Context API
* **Animations:** GSAP (ScrollTrigger, Flip, TextPlugin), Lenis Smooth Scroll
* **Styling:** Custom Modular CSS / CSS Utilities

### **Backend**
* **Framework:** Laravel 11 / PHP 8.x
* **Database:** MySQL / SQLite (Database Migrations & Seeders included)
* **API:** RESTful API Architecture
* **Environment:** Artisan CLI, Composer

---

## 📁 Repository Structure

```text
shahadat-fullstack-portfolio/
├── frontend/                 # React (Vite) Client Application
│   ├── src/
│   │   ├── api.js            # Axios / Fetch API Client Setup
│   │   ├── components/       # Reusable UI Components (Navbar, Preloader, HeroCanvas, etc.)
│   │   ├── context/          # React Context (UI, Preferences, Transitions)
│   │   ├── hooks/            # Custom Hooks (useGsap, useApi)
│   │   ├── pages/            # Page Views (Home, Projects, Blogs, Contact)
│   │   └── styles.css        # Global Styling
│   ├── package.json
│   └── vite.config.js
│
└── backend/                  # Laravel REST API Application
    ├── app/                  # Controllers, Models, Mailers, Support
    ├── config/               # Application, CORS, and Auth Configs
    ├── database/             # Migrations, Seeders, & Asset Generator Scripts
    ├── routes/               # API & Web Route Definitions (`routes/api.php`)
    └── composer.json