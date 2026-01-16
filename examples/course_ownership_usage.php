<?php

// Example usage of Course ownership attributes

/*
=== Course Ownership Attributes ===

The Course model now has several attributes to check if the authenticated user owns (is enrolled in) the course:

1. is_owned_by_user - Boolean: True if user is enrolled and has paid
2. can_access - Boolean: True if user can access course content
3. user_progress - Float: User's progress percentage (0-100)
4. user_enrollment - Object: Full enrollment details for the user

=== API Response Example ===

When fetching a course, the response will include:

{
    "status": true,
    "data": {
        "id": 1,
        "name": "Laravel Advanced Course",
        "instructor_name": "John Doe",
        "price": "299.00",
        "duration": "40.00",
        "description": "Advanced Laravel concepts...",
        
        // NEW OWNERSHIP ATTRIBUTES
        "is_owned_by_user": true,
        "can_access": true,
        "user_progress": 45.50,
        
        "user_enrollment": {
            "id": 123,
            "enrolled_at": "2025-01-20T10:00:00Z",
            "status": "active",
            "payment_status": "paid",
            "progress_percentage": 45.50,
            "completed_at": null,
            "amount_paid": "299.00",
            "payment_method": "wallet"
        }
    }
}

=== Frontend Usage Examples ===

// Check if user owns the course
if (course.is_owned_by_user) {
    // Show "Continue Learning" button
    showContinueButton();
} else {
    // Show "Enroll Now" button
    showEnrollButton();
}

// Check if user can access course content
if (course.can_access) {
    // Allow access to course stages/videos
    enableCourseContent();
} else {
    // Show payment required message
    showPaymentRequired();
}

// Show progress bar
if (course.user_progress > 0) {
    updateProgressBar(course.user_progress);
}

// Show enrollment details
if (course.user_enrollment) {
    displayEnrollmentInfo(course.user_enrollment);
}

=== Backend Usage Examples ===

// In a controller or service
$course = Course::find(1);

// Check if current user owns the course
if ($course->is_owned_by_user) {
    // User has access
}

// Check for specific user
if ($course->isOwnedByUser($userId)) {
    // Specific user has access
}

// Get enrollment for specific user
$enrollment = $course->getEnrollmentForUser($userId);

// Get course statistics
$activeEnrollments = $course->active_enrollments_count;
$completedEnrollments = $course->completed_enrollments_count;
$totalEnrollments = $course->total_enrollments_count;

=== Security Benefits ===

1. Easy access control in controllers
2. Automatic enrollment checking in API responses
3. Progress tracking for personalized experience
4. Payment verification before content access

*/

echo "Course ownership attributes implemented successfully!\n";
echo "Check the Course model for all available attributes and methods.\n";
