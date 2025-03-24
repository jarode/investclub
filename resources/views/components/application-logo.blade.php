<svg viewBox="0 0 200 50" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <!-- Definicje gradientów i filtrów -->
    <defs>
        <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#4338CA;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#7C3AED;stop-opacity:1" />
        </linearGradient>
        <linearGradient id="circleGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#4338CA;stop-opacity:0.4" />
            <stop offset="100%" style="stop-color:#7C3AED;stop-opacity:0.4" />
        </linearGradient>
        <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="1" stdDeviation="1" flood-opacity="0.3" flood-color="#000"/>
        </filter>
        <linearGradient id="linkGradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:#4338CA;stop-opacity:1" />
            <stop offset="50%" style="stop-color:#7C3AED;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#4338CA;stop-opacity:1" />
        </linearGradient>
    </defs>
    
    <!-- Tło logo z cieniem -->
    <circle cx="25" cy="25" r="23" fill="url(#circleGradient)" filter="url(#shadow)"/>
    
    <!-- Element łączący (symbolizujący "Link") -->
    <path d="M12 25 C12 18, 38 18, 38 25" 
          stroke="url(#linkGradient)" 
          stroke-width="1.5" 
          fill="none"
          stroke-dasharray="2,1"
          opacity="0.6">
        <animate attributeName="d" 
                 values="M12 25 C12 18, 38 18, 38 25;
                         M12 25 C12 32, 38 32, 38 25;
                         M12 25 C12 18, 38 18, 38 25" 
                 dur="5s" 
                 repeatCount="indefinite"/>
    </path>
    
    <!-- Symbol LINV - stylizowana litera L -->
    <path d="M13 13 L13 37 L37 37" 
          stroke="url(#logoGradient)" 
          stroke-width="4" 
          fill="none" 
          stroke-linecap="round" 
          stroke-linejoin="round"
          filter="url(#shadow)"/>
    
    <!-- Punkt L (Link) -->
    <circle cx="13" cy="13" r="3.5" fill="#4338CA">
        <animate attributeName="opacity" values="0.9;1;0.9" dur="3s" repeatCount="indefinite"/>
    </circle>
    
    <!-- Punkt I (Invest) -->
    <circle cx="25" cy="25" r="3.5" fill="#6366F1">
        <animate attributeName="r" values="3.5;4;3.5" dur="3s" repeatCount="indefinite"/>
    </circle>
    
    <!-- Punkt N (Network) -->
    <circle cx="25" cy="37" r="3.5" fill="#7C3AED">
        <animate attributeName="opacity" values="0.9;1;0.9" dur="3s" begin="1s" repeatCount="indefinite"/>
    </circle>
    
    <!-- Punkt V (Value) -->
    <circle cx="37" cy="37" r="3.5" fill="#8B5CF6">
        <animate attributeName="r" values="3.5;4;3.5" dur="3s" begin="2s" repeatCount="indefinite"/>
    </circle>
    
    <!-- Symbole inwestycji wewnątrz L -->
    <path d="M20 30 L25 22 L30 26 L35 18" 
          stroke="#6366F1" 
          stroke-width="1.5" 
          fill="none"
          stroke-linecap="round"
          opacity="0.7"/>
          
    <!-- Tekst LINV z podkreśleniem znaczenia -->
    <text x="60" y="35" 
          font-family="Arial, sans-serif" 
          font-size="32" 
          font-weight="800" 
          letter-spacing="1"
          fill="url(#logoGradient)"
          filter="url(#shadow)">
        LINV
    </text>
    
    <!-- Subtelny tekst pod logo wyjaśniający skrót -->
    <text x="60" y="43" 
          font-family="Arial, sans-serif" 
          font-size="8" 
          font-weight="500"
          letter-spacing="2"
          fill="#6B7280">
        LINK · INVEST
    </text>
</svg>
