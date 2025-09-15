# HPWays Live Server Setup

## Current Status
✅ **Server is now running live on port 8081!**

### Access URLs:
- **Main Application**: http://10.0.0.4:8081
- **Vite Hot Module Reload**: http://10.0.0.4:5173

## Features Setup:
1. **Laravel Server** running on port 8081 with host 0.0.0.0 (accessible from anywhere)
2. **Vite Development Server** running on port 5173 for hot reloading
3. **Live Updates** - Any code changes will automatically reflect in the browser
4. **Production Optimized** - Configuration cached, routes cached, views cached

## Manual Controls:

### Start Server:
```bash
./start-server.sh
```

### Stop Server:
Press `Ctrl+C` in the terminal where the server is running

### View Server Status:
```bash
sudo ss -tulpn | grep :8081  # Check Laravel server
sudo ss -tulpn | grep :5173  # Check Vite server
```

### Check Running Processes:
```bash
ps aux | grep -E "(php|node)" | grep -v grep
```

## Production Setup (Auto-start on boot):

### Install as System Service:
```bash
sudo cp hpways.service /etc/systemd/system/
sudo cp hpways-vite.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable hpways
sudo systemctl enable hpways-vite
sudo systemctl start hpways
sudo systemctl start hpways-vite
```

### Check Service Status:
```bash
sudo systemctl status hpways
sudo systemctl status hpways-vite
```

## Client Access:
Your clients can now access the application at:
**http://10.0.0.4:8081**

The application will show live updates whenever you make changes to the code!

## Notes:
- Make sure port 8081 and 5173 are open in your firewall
- The server automatically restarts if it crashes
- All Laravel optimizations are applied for better performance
