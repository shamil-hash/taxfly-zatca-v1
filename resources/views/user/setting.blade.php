<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Company Details</title>
    @include('layouts/usersidebar')
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --border-color: #dee2e6;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        #content {
            padding: 20px;
            margin-left: 250px; /* Adjust based on sidebar width */
        }
        
        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.15);
            color: #27ae60;
            border-left: 4px solid #2ecc71;
        }
        
        .form-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 30px;
            max-width: 900px;
            margin: 0 auto;
        }
        
        .form-header {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .form-header h2 {
            color: var(--dark-gray);
            font-weight: 600;
            margin: 0;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }
        
        .form-group {
            flex: 0 0 50%;
            padding: 0 15px;
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            flex: 0 0 100%;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
            background-color: #fff;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
        }
        
        .logo-upload {
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }
        
        .logo-preview-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .logo-preview {
            width: 120px;
            height: 120px;
            object-fit: contain;
            border: 1px dashed var(--border-color);
            border-radius: 4px;
            padding: 5px;
            background-color: var(--light-gray);
        }
        
        .file-upload-wrapper {
            flex-grow: 1;
        }
        
        .file-upload-label {
            display: block;
            padding: 10px;
            border: 1px dashed var(--border-color);
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background-color: var(--light-gray);
        }
        
        .file-upload-label:hover {
            border-color: var(--primary-color);
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .file-upload-label span {
            display: block;
            margin-top: 8px;
            font-size: 12px;
            color: #777;
        }
        
        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        
        .btn-submit:hover {
            background-color: var(--secondary-color);
        }
        
        .btn-submit:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            #content {
                margin-left: 0;
                padding: 15px;
            }
            
            .form-group {
                flex: 0 0 100%;
            }
            
            .logo-upload {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div id="content">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="form-container">
            <div class="form-header">
                <h2>Edit Company Details</h2>
            </div>
            
            <form id="branchForm" method="POST" action="{{ route('branch.update') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="company">Company Name</label>
                        <input type="text" id="company" name="company" class="form-control" value="{{ $branch->company ?? '' }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="branch">Location/Branch</label>
                        <input type="text" id="branch" name="branch" class="form-control" value="{{ $branch->branchname ?? '' }}" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="tr_no">TRN Number</label>
                        <input type="text" id="tr_no" name="tr_no" class="form-control" value="{{ $branch->tr_no ?? '' }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="mobile">Phone Number</label>
                        <input type="tel" id="mobile" name="mobile" class="form-control" value="{{ $branch->mobile ?? '' }}" required>
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" required>{{ $branch->address ?? '' }}</textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ $branch->email ?? '' }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="po_box">PO Box</label>
                        <input type="text" id="po_box" name="po_box" class="form-control" value="{{ $branch->po_box ?? '' }}">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <select id="currency" name="currency" class="form-control" style="height:auto;" required>
                            <option value="AED" {{ ($branch->currency ?? '') == 'AED' ? 'selected' : '' }}>AED</option>
                            <option value="SAR" {{ ($branch->currency ?? '') == 'SAR' ? 'selected' : '' }}>SAR</option>
                            <option value="INR" {{ ($branch->currency ?? '') == 'INR' ? 'selected' : '' }}>INR</option>
                            <option value="USD" {{ ($branch->currency ?? '') == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ ($branch->currency ?? '') == 'EUR' ? 'selected' : '' }}>EUR</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Company Logo</label>
                        <div class="logo-upload">
                            @if(isset($branch->logo) && $branch->logo)
                                <div class="logo-preview-container">
                                    <img src="{{ asset($branch->logo) }}" alt="Company Logo" class="logo-preview">
                                    <input type="hidden" name="existing_logo" value="{{ $branch->logo }}">
                                </div>
                            @endif
                            
                            <div class="file-upload-wrapper">
                                <label for="logo" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Click to upload new logo (Max 2MB)</span>
                                </label>
                                <input type="file" id="logo" name="logo" class="form-control" accept="image/*" style="display: none;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-submit" id="submitBtn">Update</button>
                </div>
            </form>
        </div>
    </div>


</body>
</html>