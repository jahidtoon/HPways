#!/bin/bash

# HPWays Live Server Startup Script
# This script starts both Laravel and Vite development servers

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}🚀 Starting HPWays Live Server on Port 8081${NC}"
echo -e "${YELLOW}Server will be available at: http://10.0.0.4:8081${NC}"

# Function to cleanup processes on exit
cleanup() {
    echo -e "\n${YELLOW}Stopping servers...${NC}"
    kill $LARAVEL_PID $VITE_PID 2>/dev/null
    exit 0
}

# Set trap to cleanup on exit
trap cleanup SIGINT SIGTERM

# Start Laravel server in background
echo -e "${GREEN}Starting Laravel server on port 8081...${NC}"
php artisan serve --host=0.0.0.0 --port=8081 &
LARAVEL_PID=$!

# Wait a moment for Laravel to start
sleep 3

# Start Vite development server in background
echo -e "${GREEN}Starting Vite dev server for hot reloading...${NC}"
npm run dev &
VITE_PID=$!

# Wait a moment for Vite to start
sleep 5

echo -e "${GREEN}✅ Both servers are now running!${NC}"
echo -e "${YELLOW}📱 Laravel App: http://10.0.0.4:8081${NC}"
echo -e "${YELLOW}🔄 Vite HMR: http://10.0.0.4:5173${NC}"
echo -e "${YELLOW}Press Ctrl+C to stop both servers${NC}"

# Wait for background processes
wait $LARAVEL_PID $VITE_PID
