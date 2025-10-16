<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234361ee'><path d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/></svg>"> -->
    <!-- <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>"> -->
    <!-- <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234361ee'><path d='M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z'/></svg>"> -->
    <!-- <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234361ee'><path d='M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z'/></svg>"> -->
    <link rel="shortcut icon" href="./img/5850276.png" type="image/x-icon">

    <title>Student Management System</title>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --border: #dee2e6;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --radius: 12px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 24px;
            height: calc(100vh - 40px);
        }
        
        /* Sidebar Styles */
        .sidebar {
            background: white;
            border-radius: var(--radius);
            padding: 30px 25px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }
        
        .logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary);
            margin-bottom: 15px;
        }
        
        .logo-section h1 {
            color: var(--dark);
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .logo-section p {
            color: var(--gray);
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
            background: var(--light);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            background: white;
        }
        
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 10px;
        }
        
        .btn {
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(67, 97, 238, 0.3);
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background: #e11574;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(247, 37, 133, 0.3);
        }
        
        /* Main Content Styles */
        .main-content {
            background: white;
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }
        
        .content-header h2 {
            color: var(--dark);
            font-size: 28px;
            font-weight: 700;
        }
        
        .stats {
            display: flex;
            gap: 15px;
        }
        
        .stat-card {
            background: var(--light);
            padding: 15px 20px;
            border-radius: 8px;
            text-align: center;
            min-width: 120px;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
        }
        
        .stat-label {
            font-size: 12px;
            color: var(--gray);
            text-transform: uppercase;
            margin-top: 5px;
        }
        
        .table-container {
            flex: 1;
            overflow: auto;
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }
        
        .data-table thead {
            background: var(--primary);
            color: white;
        }
        
        .data-table th {
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .data-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            color: var(--dark);
        }
        
        .data-table tbody tr {
            transition: var(--transition);
        }
        
        .data-table tbody tr:hover {
            background: rgba(67, 97, 238, 0.05);
        }
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--light);
            color: var(--gray);
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .action-btn.edit:hover {
            background: var(--success);
            color: white;
        }
        
        .action-btn.delete:hover {
            background: var(--danger);
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            color: var(--border);
        }
        
        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .dashboard {
                grid-template-columns: 1fr;
                height: auto;
            }
            
            .sidebar {
                height: auto;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }
            
            .content-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .stats {
                width: 100%;
                justify-content: space-between;
            }
            
            .stat-card {
                flex: 1;
            }
            
            .data-table {
                font-size: 14px;
            }
            
            .data-table th,
            .data-table td {
                padding: 12px 15px;
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                padding: 20px 15px;
            }
            
            .data-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard">
            <!-- Control Panel -->
            <aside class="sidebar">
                <div class="logo-section">
                    <img src="./img/privo-circle-kid-5.webp" alt="Student Management System" class="logo">
                    <h1>Student Manager</h1>
                    <p>Admin Dashboard</p>
                </div>
                
                <form action="" method="post">
                    <div class="form-group">
                        <label for="id"><i class="fas fa-id-card"></i> Student ID</label>
                        <input type="text" name="id" id="id" class="form-control" placeholder="Enter student ID">
                    </div>
                    
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Student Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter student name">
                    </div>
                    
                    <div class="form-group">
                        <label for="address"><i class="fas fa-map-marker-alt"></i> Student Address</label>
                        <input type="text" name="address" id="address" class="form-control" placeholder="Enter student address">
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" name="add" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Add Student
                        </button>
                        <button type="submit" name="del" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i> Delete Student
                        </button>
                    </div>
                </form>
            </aside>
            
            <!-- Main Content -->
            <main class="main-content">
                <div class="content-header">
                    <h2>Student Records</h2>
                    <div class="stats">
                        <div class="stat-card">
                            <div class="stat-number">127</div>
                            <div class="stat-label">Total Students</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">12</div>
                            <div class="stat-label">New This Month</div>
                        </div>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student Name</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ST2023001</td>
                                <td>John Smith</td>
                                <td>123 Main Street, New York, NY</td>
                                <td class="actions">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>ST2023002</td>
                                <td>Emily Johnson</td>
                                <td>456 Oak Avenue, Los Angeles, CA</td>
                                <td class="actions">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>ST2023003</td>
                                <td>Michael Brown</td>
                                <td>789 Pine Road, Chicago, IL</td>
                                <td class="actions">
                                    <button class="action-btn edit"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Simple animation for buttons
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Sample data for demonstration
        console.log('Student Management System Loaded');
    </script>
</body>
</html>