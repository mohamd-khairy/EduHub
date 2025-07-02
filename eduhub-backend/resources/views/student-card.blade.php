<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Card</title>
    <style>
        /* Adjust for PDF Compatibility */
        .card {
            width: 300px;
            padding: 20px;
            background-color: white;
            border: 1px solid #ccc; /* Instead of box-shadow, use border for better compatibility */
            border-radius: 10px;
            text-align: center;
        }

        .image-container {
            width: 120px;
            height: 120px;
            border-radius: 50%; /* Ensure rounded images */
            overflow: hidden;
            margin-bottom: 20px;
        }

        .student-image {
            width: 100%;
            height: 100%;
            object-fit: cover; /* DomPDF may not support this, try an image with correct dimensions */
        }

        .details {
            margin-bottom: 20px;
        }

        .student-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .details p {
            font-size: 14px;
            color: #555;
        }

        /* PDF-specific styles */
        body {
            font-family: Arial, sans-serif;
        }
    </style>
</head>

<body>
    <div class="card">
        <!-- Student Image -->
        <div class="image-container">
            <!-- Dynamically rendering student image -->
            <img src="{{ url($student->image) }}" srcset="{{ url($student->image) }}" alt="{{ $student->name }}" class="student-image" />
        </div>

        <!-- Student Details -->
        <div class="details">
            <h2 class="student-name">{{ $student->name }}</h2>
            <p><strong>Phone:</strong> {{ $student->phone }}</p>
            <p><strong>Grade Level:</strong> {{ $student->grade_level }}</p>
        </div>
    </div>
</body>

</html>
