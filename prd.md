1. Executive Summary
Sportify is a web-based sports venue rental marketplace that connects venue owners (Sellers) with individuals and groups looking to book sports facilities (Customers). The platform also provides an administrative layer (Admin) for platform governance, ensuring listing quality and user trust.
The product is built on a PHP backend with an HTML/CSS/JavaScript frontend powered by the MDBootstrap component library and the Poppins typeface. It supports three distinct roles — Customer, Seller, and Admin — each with a dedicated dashboard and feature set.
Core Value Proposition: Sportify eliminates the friction of offline venue booking by giving sports enthusiasts instant visibility into real-time slot availability, verified listings, and seamless confirmation — all in one platform.

2. Goals & Objectives
Business Goals
Provide a scalable, multi-tenant marketplace for sports facility rental across multiple sport categories. Enable venue owners to monetise underutilised slots and grow their customer base digitally. Give the platform administrator complete oversight and control over all listings, users, and bookings.
User Goals
Customers want to discover, evaluate, and book sports venues effortlessly in a few clicks. Sellers want to list, manage, and promote venues and tournaments with minimal technical knowledge. Admins need to maintain platform health by reviewing accounts, managing listings, and dismissing problematic bookings.
Success Metrics
Venue listing activation rate should reach at least 80% of registered sellers by month three. Booking confirmation rate should be 70% or higher of sessions started. Search-to-booking conversion should reach 25%. Admin dismissal response time should stay under 24 hours. The average venue rating across the platform should be 4.0 out of 5.0 or higher.

3. Scope
In Scope for v1.0
The first release covers a three-role authentication system for Customer, Seller, and Admin. It includes customer venue browsing with search and filters, slot-based booking with real-time availability enforcement, and a booking confirmation page. Sellers get full venue and tournament listing management with photo uploads and enable/disable toggles. The Admin gets a dashboard for user management, listing moderation, and booking dismissal. A review and rating system, a live revenue tracking panel for Sellers, and a booking confirmation page for Customers are all included.
Out of Scope for v1.0
In-app payment gateway or wallet functionality, native mobile applications, third-party calendar integrations, automated SMS or email notifications, and an AI-powered venue recommendation engine are all deferred to future releases.

4. User Personas
Arjun, 26 — Customer
Arjun is an amateur cricketer who wants to find and book a turf for weekend cricket with his friends. His main pain point is having to call multiple venues just to check whether a slot is available on a given day. He is highly comfortable with apps and expects a fast, intuitive experience similar to food delivery or ride-hailing apps he uses daily.
Priya, 38 — Seller
Priya owns a multi-sport complex and wants to fill empty slots and grow her revenue digitally. Currently she manages bookings over WhatsApp and phone calls, which leads to double-bookings and missed enquiries. She has moderate tech comfort and needs a simple dashboard with no steep learning curve.
Admin
The platform administrator oversees all activity on Sportify. They review flagged listings, suspend problematic accounts, dismiss fraudulent bookings, and ensure the overall quality and safety of the marketplace.

5. System Architecture
Technology Stack
The backend runs on PHP 8.x using REST-style endpoints and PHP session management. The frontend is built with HTML5, CSS3, and Vanilla JavaScript (ES6+). The UI component library is MDBootstrap 5, which combines Material Design with Bootstrap 5. Typography is handled by Google Fonts — Poppins in weights 300, 400, 500, 600, and 700. The database is MySQL or MariaDB. File storage for venue photos uses local server storage in v1.0. Authentication is handled through PHP sessions with role-based access control.
High-Level Architecture Flow
The application follows a traditional MVC-inspired PHP architecture. The browser sends HTTP requests to PHP route handlers. Those handlers call service and model classes that interact with the MySQL database. Processed data is returned to the PHP view layer, which renders HTML templates using MDBootstrap components. JavaScript handles dynamic UI behaviours — search filtering, slot availability checks, and revenue chart rendering — via AJAX calls to PHP JSON endpoints.
Database Entities
The core entities are users (id, name, email, password hash, role, status, created date), venues (id, seller id, name, sport type, location, description, price per slot, average rating, active status, created date), venue photos (id, venue id, photo URL, sort order), tournaments (id, seller id, name, sport type, location, description, start and end dates, active status), bookings (id, customer id, venue id, slot date, slot start, slot end, status, created date), reviews (id, customer id, venue id, booking id, rating from 1 to 5, comment, created date), and revenue log (id, seller id, booking id, amount, recorded date).

6. Authentication & Role Management
Login Page
The single login page at /login serves all three roles. A role selector — presented as radio buttons or tabs — lets the user identify as Customer, Seller, or Admin before submitting. On successful credential verification, a PHP session is initialised with the user's role and they are redirected to the appropriate dashboard. Failed logins show an MDBootstrap inline alert. Passwords are hashed using PHP's password_hash() with PASSWORD_BCRYPT. Sessions expire after two hours of inactivity. The admin account is pre-seeded with the credentials admin@gmail.com and admin@123.
Role-Based Access Control
Customers can browse venues, book slots, and leave reviews on completed bookings. Sellers can list and manage their own venues and tournaments and view their own revenue. Admins can manage all user accounts, moderate all listings, and dismiss any booking. Sellers cannot book slots and customers cannot list venues. No role other than Admin has cross-account visibility.

7. Customer Module
Dashboard Layout
The Customer dashboard is divided into four primary sections accessible via a top navigation bar and sidebar: Profile, Browse and Search, Available Venues, and Past Bookings.
Profile
The profile section shows the customer's full name, email (read-only), phone number, profile picture, and city. An edit mode activates an MDBootstrap form with inline validation. Profile pictures can be uploaded in JPG or PNG format up to 2 MB.
Search Bar & Filters
The search bar sits prominently at the top of the Browse section and queries venue names, sport types, and locations. The filter panel — rendered as an MDBootstrap collapsible accordion or sidebar — includes a Sport Type multi-select (Cricket, Football, Badminton, Basketball, Tennis, Swimming, Others), a Location text field or city dropdown, a date picker that filters to venues with at least one open slot on the selected date, a time range selector, a dual-handle price range slider showing price per slot, and a star rating filter for minimum rating thresholds.
Venue Listing Grid
Search results render as an MDBootstrap card grid — three columns on desktop, two on tablet, one on mobile. Each card shows the primary venue photo, venue name, sport type badge, location, average star rating with total review count, price per slot, and a Book Now call-to-action button.
Availability Rule
If all time slots for a selected date at a venue are already booked, the Book Now button is replaced with a greyed-out Fully Booked badge. The venue card remains visible in search results for discovery purposes but cannot be selected for booking.
Venue Detail & Booking Flow
Clicking a venue card opens the Venue Detail page. This page shows a photo carousel, the full description and seller caption, an amenities list, a Google Maps iframe for location, and the customer reviews section. The slot picker shows a calendar date selector followed by available time-slot chips; booked slots are visually disabled with a greyed non-clickable state. A booking summary sidebar shows the selected date, slot, venue name, and price. Clicking Confirm Booking triggers a PHP POST request, creates the booking record in the database, and redirects to the Booking Confirmation page.
Booking Confirmation Page
The confirmation page displays a prominent success card with a green checkmark icon, the booking reference number, venue name, date, time slot, location, and the seller's contact details. Two CTA buttons are shown: View My Bookings and Browse More Venues.
Past Bookings
The Past Bookings section shows a sortable, paginated MDBootstrap DataTable of all bookings with columns for booking reference, venue name, date, slot time, status (Confirmed or Dismissed), and an action column. Customers can leave a review from this table only for Confirmed bookings that have not yet been reviewed.

8. Seller Module
Dashboard Layout
The Seller dashboard is divided into four sections: Profile, My Venues, My Tournaments, and Revenue Tracker.
Profile
The profile captures business name, owner name, email, phone, city, and a profile or logo image. Editing behaviour mirrors the Customer profile flow.
Venue Management
The Add New Venue form collects the venue name, sport type from a dropdown, location or address with an optional map pin, a description or caption in a rich textarea, price per slot in INR, slot duration (30 minutes, 1 hour, or 2 hours), operating hours, and a multi-file photo upload (minimum one, maximum ten photos in JPG or PNG format, up to 5 MB each).
Each existing venue card or table row provides three controls: an Edit button that reopens the form pre-filled, an Enable/Disable toggle switch (MDBootstrap switch component) that removes the venue from customer search results immediately when disabled, and a Delete option that performs a soft delete with a confirmation modal.
Tournament Management
The Add New Tournament form collects tournament name, sport type, location, description or caption, start date, end date, registration deadline, and photos following the same upload rules as venues. Existing tournament controls mirror venue controls with Edit, Enable/Disable, and Delete options.
Live Revenue Tracker
The Revenue Tracker panel displays earnings derived from confirmed bookings across the seller's venues. It refreshes on page load with an optional manual AJAX refresh button. The panel shows four key metrics: Today's Revenue (sum of confirmed bookings with today's slot date), This Week's Revenue (rolling 7-day window), This Month's Revenue (current calendar month total), and All-Time Revenue (total lifetime earnings). A bar chart built with MDBootstrap and Chart.js visualises daily revenue for the last 30 days. A Recent Transactions table shows the last ten bookings with venue name, customer name, slot date, and amount.

9. Admin Module
Credentials
The admin account is hardcoded with the email admin@gmail.com and the password admin@123. For production, these should be replaced with environment-variable-based seeding and a forced password change on first login.
Dashboard Overview
The Admin dashboard is a management console with four sections: User Management, Venue Listings, Tournament Listings, and Bookings.
User Management
A tabbed interface separates Sellers and Customers. Each row shows the user's ID, name, email, join date, and account status. Available actions are Suspend or Activate (a toggle), View Profile, and Delete Account. Suspending a seller account automatically disables all of their active listings.
Venue Listings
All venues across all sellers are displayed sorted in ascending order by average rating, meaning the lowest-rated venues appear first to facilitate quality review. Table columns include venue name, seller name, sport type, location, average rating, active status, and created date. Actions available are View Details and Dismiss or Remove Listing. Dismissing a listing soft-deletes it and changes the on-screen status immediately.
Tournament Listings
The tournament listings section mirrors the venue listings layout but is sorted by start date with the most recent first.
Booking Management
All platform bookings are shown in a filterable MDBootstrap DataTable with columns for booking reference, customer name, venue name, slot date, slot time, and status. The admin can filter by date range, status, or seller. The Dismiss Booking action changes the booking status to dismissed and frees the slot for re-booking by another customer.

10. Reviews & Ratings System
Only customers with a confirmed, non-dismissed booking for a venue may submit a review, and they are limited to one review per booking. The review form requires a star rating from 1 to 5 and accepts an optional text comment of up to 500 characters. Once submitted, a review cannot be edited by the customer, though the Admin can remove abusive reviews. The venue's average rating is calculated as the mean of all approved review ratings and stored in the venues table for query performance. On the Venue Detail page, reviews are displayed in reverse-chronological order showing the reviewer's first name and booking date.

11. UI / UX Design Guidelines
Design System
The platform uses Poppins from Google Fonts in weights 300 through 700. MDBootstrap 5 provides all core components including cards, modals, navbars, tables, forms, and badges. The primary colour is a deep green at #1A6B3C to reflect a sports theme. The accent colour is amber at #F5A623 for call-to-action buttons and highlights. Backgrounds use white and MDBootstrap's default light grey #F8F9FA. Cards use an 8px border radius and inputs use 4px. Icons come from the Material Icons library bundled with MDBootstrap.
Responsive Behaviour
All layouts follow a mobile-first approach using MDBootstrap's responsive grid. Navigation collapses to a hamburger menu on mobile. The venue grid adapts from three columns on desktop to two on tablet and one on mobile. Revenue charts scroll horizontally on small screens.
Key Interaction Patterns
Slot availability is checked via an AJAX call on date selection; booked slots render with MDBootstrap's disabled state class with no page reload. Search is triggered on input with a 300ms debounce and on any filter change. The venue enable/disable toggle fires an AJAX POST request with no page reload. Booking confirmation uses a full page redirect rather than a modal, producing a clean, shareable confirmation URL.

12. Non-Functional Requirements
Page load time should be under 3 seconds on a 4G connection and AJAX search responses should return within 500 milliseconds. All user inputs must be sanitised using PDO prepared statements. CSRF tokens are required on all forms. Passwords must be hashed with bcrypt. The database should be indexed on venues.rating_avg, bookings.slot_date, and users.role for query performance. The platform targets 99% uptime and must be deployable on standard shared PHP hosting. Browser support covers Chrome 110 and above, Firefox 110 and above, Safari 15 and above, and Edge 110 and above. The UI must meet WCAG 2.1 AA colour contrast standards and all form fields must have associated labels.

13. Suggested File & Folder Structure
The entry point is index.php, which redirects users to the login page or their dashboard based on session state. The login page lives at login.php. Dashboard pages are separated by role into dashboard/customer/, dashboard/seller/, and dashboard/admin/. PHP JSON endpoints for AJAX requests — covering search, slot availability, and revenue data — live in an api/ directory. PHP model classes for User, Venue, Booking, and Review live in models/. Custom styles sit in assets/css/ on top of MDBootstrap, custom JavaScript modules in assets/js/, and uploaded venue and profile photos in assets/uploads/. Database connection configuration is isolated in config/db.php.

14. Development Phases & Milestones
Phase 1 covers the foundation: database schema, PHP authentication system, login page, and session management. Estimated duration is one week. Phase 2 delivers the full Customer module including search, venue browsing, slot booking, the confirmation page, and past bookings, estimated at one and a half weeks. Phase 3 delivers the Seller module covering venue and tournament CRUD, photo uploads, enable/disable functionality, and the revenue tracker, also estimated at one and a half weeks. Phase 4 delivers the Admin module with user management, listing moderation, and booking dismissal, estimated at one week. Phase 5 covers the review system, UI refinement, and responsive testing, estimated at one week. Phase 6 is QA and deployment including cross-browser testing, a security audit, and production deployment, estimated at half a week. Total estimated timeline is approximately six and a half weeks.

15. Open Questions & Assumptions
The primary assumption for v1.0 is that there is no payment gateway — bookings are reservations only and payment is handled offline between the customer and seller. Slot availability is managed entirely by the platform; sellers cannot manually block individual slots outside of the enable/disable toggle on the full listing. All monetary values are in Indian Rupees. The admin account is the only superuser and multi-admin support is not included in v1.0.
Open questions that need team alignment include: whether customers should receive an email or SMS confirmation after booking (requires SMTP or SMS gateway, suggested for v1.1); what the slot conflict resolution strategy should be if two customers attempt to book the same slot simultaneously (a database-level unique constraint on venue id, slot date, and slot start is recommended); whether sellers should be able to set custom cancellation policies per venue (deferred to v1.1); and whether there should be a public venue directory accessible without logging in.

16. Glossary
A slot is a fixed time block available for booking at a venue, for example 6 PM to 7 PM. A listing is a venue or tournament published by a Seller on the platform. To dismiss means an Admin action to invalidate a booking or remove a listing from the platform. Enable/Disable refers to the Seller toggle that shows or hides a venue or tournament from customer search results. The Revenue Tracker is the real-time earnings dashboard visible only to the Seller. RBAC stands for Role-Based Access Control, which restricts features based on user role. MDB refers to MDBootstrap, the UI component library used across the platform.