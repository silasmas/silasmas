/**
 * Visuel décoratif pour le bandeau d'annonce Academy (remplace une photo).
 * Fond sombre + halos accent, motif abstrait code/IA, entièrement vectoriel.
 */
export function AcademyPromoGraphic() {
  return (
    <svg
      viewBox="0 0 1600 686"
      preserveAspectRatio="xMidYMid slice"
      className="absolute inset-0 size-full"
      aria-hidden
    >
      <defs>
        <linearGradient id="apg-bg" x1="0" y1="0" x2="1600" y2="686" gradientUnits="userSpaceOnUse">
          <stop offset="0" stopColor="#120d08" />
          <stop offset="1" stopColor="#0a0a0a" />
        </linearGradient>
        <radialGradient id="apg-glow-1" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(1220 120) rotate(90) scale(520)">
          <stop offset="0" stopColor="#ff5b1f" stopOpacity="0.55" />
          <stop offset="1" stopColor="#ff5b1f" stopOpacity="0" />
        </radialGradient>
        <radialGradient id="apg-glow-2" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(160 620) rotate(-90) scale(420)">
          <stop offset="0" stopColor="#b45309" stopOpacity="0.5" />
          <stop offset="1" stopColor="#b45309" stopOpacity="0" />
        </radialGradient>
        <pattern id="apg-grid" width="46" height="46" patternUnits="userSpaceOnUse">
          <path d="M46 0H0V46" fill="none" stroke="#ffffff" strokeOpacity="0.06" strokeWidth="1" />
        </pattern>
      </defs>

      <rect width="1600" height="686" fill="url(#apg-bg)" />
      <rect width="1600" height="686" fill="url(#apg-grid)" />
      <rect width="1600" height="686" fill="url(#apg-glow-1)" />
      <rect width="1600" height="686" fill="url(#apg-glow-2)" />

      {/* Motif code décoratif, aligné à droite */}
      <g opacity="0.5" fontFamily="var(--font-mono, monospace)">
        <text x="1080" y="150" fontSize="64" fill="#ffffff" opacity="0.08" fontWeight="700">
          {"<AI/>"}
        </text>
        <text x="1150" y="330" fontSize="46" fill="#ff5b1f" opacity="0.25" fontWeight="600">
          {"{ }"}
        </text>
        <text x="1020" y="470" fontSize="40" fill="#ffffff" opacity="0.1" fontWeight="600">
          {"01100"}
        </text>
        <text x="1300" y="560" fontSize="30" fill="#ffffff" opacity="0.12" fontWeight="600">
          {"/>"}
        </text>
      </g>

      {/* Constellation de nœuds — évoque un réseau / IA */}
      <g stroke="#ff5b1f" strokeOpacity="0.35" strokeWidth="1.5">
        <path d="M1180 90 L1310 200 L1260 340 L1400 260" fill="none" />
        <path d="M1310 200 L1440 150" fill="none" />
      </g>
      <g fill="#ff5b1f">
        <circle cx="1180" cy="90" r="5" opacity="0.8" />
        <circle cx="1310" cy="200" r="6" opacity="0.9" />
        <circle cx="1260" cy="340" r="4" opacity="0.6" />
        <circle cx="1400" cy="260" r="5" opacity="0.7" />
        <circle cx="1440" cy="150" r="4" opacity="0.6" />
      </g>
    </svg>
  );
}
