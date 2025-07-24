# Video File Upload Configuration

To handle video file uploads properly, you may need to adjust the following settings:

## 1. PHP Configuration (php.ini)
```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
```

## 2. Web Server Configuration

### For Apache (.htaccess in public folder)
```apache
php_value upload_max_filesize 50M
php_value post_max_size 50M
php_value max_execution_time 300
php_value max_input_time 300
```

### For Nginx
```nginx
client_max_body_size 50M;
```

## 3. Laravel Configuration

The controller already includes validation for:
- Video file types: mp4, avi, mov, wmv, flv, webm, mkv
- Maximum file size: 50MB (51200 KB)
- Thumbnail images: jpeg, png, jpg, gif, svg, webp (max 2MB)

## 4. Storage
Video files and thumbnails are stored in: `storage/app/public/home_videos/`

Make sure to run: `php artisan storage:link` to create the symbolic link for public access.
