<!DOCTYPE html>
<html>
<head>
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none !important;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
            overflow: hidden;
        }
    </style>
</head>
<body>

    <div class="no-scrollbar" style="
        width: 1000px; 
        height: 600px; 
        border: 2px solid #ccc; 
        border-radius: 8px; 
        background: #f9f9f9;
    ">
        <iframe 
            src="https://drive.google.com/file/d/1uLQ7ntns9zpSVJ9VoK_IyYXMII7R_Ne4/preview" 
            width="1000" 
            height="600"
            style="
                border: none; 
                transform: scale(0.4286); 
                transform-origin: 0 0; 
                pointer-events: none;
            "
            loading="lazy">
        </iframe>
    </div>

</body>
</html>