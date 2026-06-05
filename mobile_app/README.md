# LK Healthcare — Flutter Mobile App

Premium mobile companion to the LK Healthcare Laravel backend. Built with Flutter 3.19+, Provider state management, Material 3, and Google Fonts for a polished healthcare UX.

## Features

- **4 user roles** — Patient, Doctor, Pharmacist, Admin
- **40+ screens** covering every web flow, mobile-optimized
- **Premium UI** — gradient headers, animated SOS, role-coloured palettes, Material 3
- **Responsive** — clamps to phone width even on tablets
- **Controllers ready for backend** — each controller has clearly marked integration points so wiring real APIs is trivial

## Folder structure

```
mobile_app/
├── lib/
│   ├── main.dart              # entry + Provider tree
│   ├── app.dart               # MaterialApp + responsive frame
│   ├── config/                # theme, colors, routes, API endpoints
│   ├── models/                # data classes (User, Doctor, Appointment, …)
│   ├── controllers/           # ChangeNotifier controllers w/ backend hooks
│   ├── services/              # ApiService + StorageService (Sanctum-ready)
│   ├── widgets/               # reusable cards, buttons, badges, scaffold
│   ├── utils/                 # helpers, validators, constants
│   └── screens/
│       ├── auth/              # login, register, forgot password
│       ├── public/            # home, about, services, doctors, contact
│       ├── patient/           # dashboard, profile, appts, Rx, lab, pharmacy, records
│       ├── doctor/            # dashboard, profile, appts, availability, Rx
│       ├── pharmacist/        # dashboard, prescriptions queue, inventory
│       ├── admin/             # dashboard + 8 management screens
│       └── shared/            # blood bank, emergency SOS, notifications, payment, receipt
```

## Setup

```bash
cd mobile_app
flutter pub get
flutter run
```

### Initialise platform folders (first time only)

If `android/` and `ios/` folders don't exist yet, recreate them with:

```bash
flutter create --platforms=android,ios .
```

This generates the native scaffolding without touching `lib/`, `pubspec.yaml`, or your code.

## Backend wiring

The Laravel backend serves the mobile app at `/api/v1/*` (Sanctum-protected). Endpoints are catalogued in [lib/config/api_config.dart](lib/config/api_config.dart).

Update the base URL for your environment:

```dart
// lib/config/api_config.dart
static const String baseUrl = 'http://10.0.2.2:8000'; // Android emulator → host
// 'http://127.0.0.1:8000'   for iOS simulator
// 'http://<your-LAN-IP>:8000' for physical device
```

Every controller contains comments marking the exact spot to replace mock data with `ApiService` calls. Search the codebase for `=== Backend integration point ===` to find them all.

## Demo login

The app currently runs against mocked controllers. Any email + password works — pick a role on the login screen to land on that role's dashboard.

## Screens at a glance

- **Splash → Onboarding → Login/Register**
- **Patient flow:** Dashboard with quick actions → book doctor → pick slot → pay → view receipt → see prescription → order medicines → upload records → emergency SOS
- **Doctor flow:** Dashboard with today's stats → manage availability → review appointments → create prescription
- **Pharmacist flow:** Queue of prescriptions → pack/ship → low-stock alerts → inventory CRUD
- **Admin flow:** Console with KPIs → manage patients, doctors, lab tests, medicines, blood bank, emergency requests, contact messages

## Tech notes

- Every screen is a `StatefulWidget`
- State is provided through `Provider` + `ChangeNotifier` — controllers expose state and methods, screens only render
- Navigation uses **named routes** declared in [lib/config/routes.dart](lib/config/routes.dart)
- `RoleScaffold` (lib/widgets/role_scaffold.dart) gives a consistent app bar, drawer, and notification indicator across all authenticated screens
- `AppDrawer` shows role-specific menu items dynamically
