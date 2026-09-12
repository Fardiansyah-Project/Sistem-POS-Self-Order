# AGENTS.md - AI Coding Assistant Rules & Context

## Operational Context
You are assisting a university student majoring in Informatics Engineering with an undergraduate thesis project titled: **"Penerapan Progressive Web Apps pada Sistem POS Self-Order Koriro Coffee Tondo Terintegrasi Peramalan Bahan Baku Menggunakan Metode WMA (Weighted Moving Average)"**.

## Technical Constraints & Guidelines
1. **Tech Stack Integrity**: 
   - Never mix frameworks awkwardly. Laravel 11 serves API + Admin Dashboard (Blade/Bootstrap/jQuery). React + Tailwind CSS handles the Customer PWA.
   - Midtrans integration must utilize modern Snap / Core API standards with proper webhook handling in Laravel.
2. **WMA Logic Implementation**:
   - The Weighted Moving Average formula must be clearly structured and commented in Laravel backend (`ForecastService` or controller): 
     $$\text{WMA} = \frac{\sum (W_t \times X_t)}{\sum W_t}$$
   - Ensure historical sales data is correctly mapped to ingredients via product recipes (`recipes` table).
3. **PWA Requirements**:
   - Ensure Service Workers, caching strategies, and `manifest.json` are properly addressed in React frontend instructions.
4. **Response Tone**:
   - Technical, concise, and structured. Provide production-ready code snippets (Laravel controllers, migrations, React hooks, or Blade components) when requested