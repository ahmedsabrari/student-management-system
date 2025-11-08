<?php

use App\Core\Env;

/**
 * File Uploads Configuration
 *
 * This file contains settings for handling file uploads. It defines storage paths,
 * file size limits, and allowed MIME types to ensure security and consistency
 * across the application.
 */
return [

    /**
     * Maximum File Size
     * The maximum file size in bytes for a single upload.
     * Default: 5MB (5 * 1024 * 1024 = 5242880 bytes).
     * Env: MAX_UPLOAD_SIZE
     */
    'max_file_size' => (int) Env::get('MAX_UPLOAD_SIZE', 5242880),

    /**
     * Base Uploads Path
     * The main directory where all uploaded files are stored.
     * It's recommended to use an absolute path for reliability.
     */
    'uploads_path' => BASE_PATH . '/public/uploads/',

    /**
     * Avatars Subdirectory Path
     * Specific directory for storing user profile pictures.
     */
    'avatars_path' => BASE_PATH . '/public/uploads/avatars/',

    /**
     * Documents Subdirectory Path
     * Specific directory for storing general documents (e.g., student records).
     */
    'documents_path' => BASE_PATH . '/public/uploads/documents/',

    /**
     * Assignments Subdirectory Path
     * Specific directory for storing student assignment submissions.
     */
    'assignments_path' => BASE_PATH . '/public/uploads/assignments/',

    /**
     * Allowed Image Types
     * An array of allowed file extensions for image uploads.
     * These should be validated against their MIME types as well for security.
     */
    'allowed_image_types' => [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp',
    ],

    /**
     * Allowed Document Types
     * An array of allowed file extensions for document uploads.
     */
    'allowed_document_types' => [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'ppt',
        'pptx',
        'txt',
    ],

];