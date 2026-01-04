<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Calendar | ERP System</title>
    @include('layouts/usersidebar')
    <style>
        /* ERP Standard Styling */
        .erp-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px;
            margin: 10px;
        }
        
        .erp-header {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-bottom: 1px solid #e0e6ed;
        }
        
        @media (min-width: 768px) {
            .erp-header {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }
        
        .erp-title {
            font-size: 18px;
            font-weight: 600;
            color: #187f6a;
        }
        
        @media (min-width: 768px) {
            .erp-title {
                font-size: 20px;
            }
        }
        
        .erp-btn {
            background: #187f6a;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .erp-btn:hover {
            background: #136a58;
        }

        .month-navigation {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 10px 5px;
            margin-bottom: 15px;
            -webkit-overflow-scrolling: touch;
        }

        .month-nav-item {
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
            white-space: nowrap;
            text-decoration: none;
            color: inherit;
            font-size: 13px;
        }

        @media (min-width: 768px) {
            .month-nav-item {
                padding: 8px 12px;
                font-size: 14px;
            }
        }

        .month-nav-item:hover {
            background: #f0f7f5;
        }

        .month-nav-item.current {
            background: #187f6a;
            color: white;
        }
        
        /* Calendar Grid */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }
        
        @media (min-width: 768px) {
            .calendar-grid {
                gap: 10px;
            }
        }
        
        .weekday-header {
            text-align: center;
            font-weight: 600;
            padding: 8px 2px;
            background: #f8f9fa;
            color: #555;
            font-size: 12px;
            border-radius: 4px;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        
        @media (min-width: 768px) {
            .weekday-header {
                padding: 10px;
                font-size: 14px;
            }
        }
        
        .calendar-day {
            border: 1px solid #e0e6ed;
            border-radius: 6px;
            padding: 5px;
            min-height: 60px;
            background: white;
            transition: all 0.2s ease;
            position: relative;
        }
        
        @media (min-width: 768px) {
            .calendar-day {
                min-height: 100px;
                padding: 10px;
            }
        }
        
        .calendar-day:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .day-number {
            font-weight: 600;
            margin-bottom: 3px;
            font-size: 12px;
        }
        
        @media (min-width: 768px) {
            .day-number {
                font-size: 14px;
                margin-bottom: 5px;
            }
        }
        
        .sale-count {
            background: #e8f5e9;
            color: #187f6a;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 10px;
            display: inline-block;
        }
        
        @media (min-width: 768px) {
            .sale-count {
                padding: 3px 6px;
                font-size: 12px;
            }
        }
        
        .today {
            border: 2px solid #187f6a;
            background: #f0f7f5;
        }
        
        /* Stats Summary */
        .stats-summary {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        @media (min-width: 768px) {
            .stats-summary {
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
            }
        }
        
        .summary-card {
            background: white;
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-top: 3px solid #187f6a;
        }
        
        .download-hint {
            text-align: center;
            margin: 10px 0;
            padding: 8px;
            background: #f0f7f5;
            border-radius: 4px;
            color: #187f6a;
            font-size: 13px;
        }
        
        @media (min-width: 768px) {
            .download-hint {
                font-size: 14px;
                margin: 15px 0;
            }
        }
        
        /* Empty day cells */
        .calendar-day.empty {
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <div id="content">
        <div class="erp-container">
            <div class="erp-header">
                <div class="erp-title">Sales Calendar - {{ $monthName }}</div>
                <div>
                    <a href="{{ url()->current() }}" class="erp-btn">
                        View Today
                    </a>
                </div>
            </div>

            <!-- Month Navigation -->
            <div class="month-navigation">
                @foreach($monthNavigation as $month)
                    <a href="{{ url()->current() }}?selected_month={{ $month['value'] }}" 
                    class="month-nav-item {{ $month['is_current'] ? 'current' : '' }}">
                        {{ $month['display'] }}
                    </a>
                @endforeach
            </div>
                        
            <!-- Stats Summary -->
            <div class="stats-summary">
                <div class="summary-card">
                    <div style="font-size: 13px; color: #777;">Monthly Sales Total</div>
                    <div style="font-size: 20px; font-weight: 600;">{{ number_format($salesData->sum('total_sales')) }}</div>
                </div>
                <div class="summary-card">
                    <div style="font-size: 13px; color: #777;">Highest Sales Day</div>
                    <div style="font-size: 20px; font-weight: 600;">
                        @php
                            $maxSales = $salesData->max('total_sales');
                            echo $maxSales ? date('M j', strtotime($currentYear.'-'.$currentMonth.'-'.$salesData->where('total_sales', $maxSales)->first()->day)) : '-';
                        @endphp
                    </div>
                </div>
                <div class="summary-card">
                    <div style="font-size: 13px; color: #777;">Average Daily Sales</div>
                    <div style="font-size: 20px; font-weight: 600;">
                        {{ number_format(round($salesData->avg('total_sales'))) }}
                    </div>
                </div>
            </div>
            
            <!-- Download Hint -->
            <div class="download-hint">
                Click on any day to download that day's sales report
            </div>
            
            <!-- Calendar Grid -->
            <div class="calendar-grid">
                <!-- Weekday headers -->
                <div class="weekday-header">Sun</div>
                <div class="weekday-header">Mon</div>
                <div class="weekday-header">Tue</div>
                <div class="weekday-header">Wed</div>
                <div class="weekday-header">Thu</div>
                <div class="weekday-header">Fri</div>
                <div class="weekday-header">Sat</div>
                
                <!-- Calendar days -->
                @php
                    $firstDay = date('w', strtotime($currentYear . '-' . $currentMonth . '-01'));
                    $daysInMonth = date('t', strtotime($currentYear . '-' . $currentMonth . '-01'));
                    $todayDay = date('j', strtotime($today));
                @endphp
                
                <!-- Empty cells for days before the 1st -->
                @for($i = 0; $i < $firstDay; $i++)
                    <div class="calendar-day empty"></div>
                @endfor
                
                <!-- Days of the month -->
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $isToday = ($day == $todayDay && $currentMonth == date('n', strtotime($today)) && $currentYear == date('Y', strtotime($today)));
                        $daySales = $salesData[$day]->total_sales ?? 0;
                        $dateString = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                        $formattedDate = date('M j, Y', strtotime($dateString));
                    @endphp
                    <div class="calendar-day {{ $isToday ? 'today' : '' }}" title="Click to download sales report for {{ $formattedDate }}">
                        <a href="/export-sales-day/{{ $userid }}/1/{{ $location }}/{{ $dateString }}" 
                        style="display: block; height: 100%; text-decoration: none; color: inherit;">
                            <div class="day-number">{{ $day }}</div>
                            @if($daySales > 0)
                                <div class="sale-count">{{ number_format($daySales) }} sale{{ $daySales > 1 ? 's' : '' }}</div>
                            @endif
                        </a>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</body>
</html>