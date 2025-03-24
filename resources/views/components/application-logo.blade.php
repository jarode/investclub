<svg viewBox="0 0 200 50" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <!-- Definicje gradientów i filtrów -->
    <defs>
        <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#4338CA;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#7C3AED;stop-opacity:1" />
        </linearGradient>
        <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="1" stdDeviation="1" flood-opacity="0.4" flood-color="#000"/>
        </filter>
        <linearGradient id="linkGradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:#4338CA;stop-opacity:1" />
            <stop offset="50%" style="stop-color:#7C3AED;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#4338CA;stop-opacity:1" />
        </linearGradient>
    </defs>
    
    <!-- Tylko 3 punkty układające się w literę L -->
    <circle cx="10" cy="10" r="6" fill="#4338CA"/>
    <circle cx="10" cy="40" r="6" fill="#6366F1"/>
    <circle cx="40" cy="40" r="6" fill="#7C3AED"/>
    
    <!-- Linie łączące punkty w kształt litery L -->
    <path d="M10 10 L10 40 L40 40" 
          stroke="url(#linkGradient)" 
          stroke-width="3" 
          fill="none"
          stroke-linecap="round"
          opacity="0.9" />
          
    <!-- Tekst LIN+V -->
    <text x="60" y="35" 
          font-family="Arial, sans-serif" 
          font-size="36" 
          font-weight="900" 
          letter-spacing="1.2"
          fill="url(#logoGradient)"
          filter="url(#shadow)">
        LIN<tspan dy="-5" dx="-2">+</tspan><tspan dy="5" dx="-2">V</tspan>
    </text>
</svg>
