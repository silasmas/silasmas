"use client";

import { useMemo, useState } from "react";
import { COUNTRIES, DEFAULT_COUNTRY } from "@/data/countries";

interface CountrySelectProps {
  value: string;
  onChange: (value: string) => void;
  className?: string;
}

const triggerClass =
  "w-full rounded-xl border border-line bg-bg-elev px-4 py-3.5 text-left text-ink outline-none transition-colors focus:border-accent";

/**
 * Liste déroulante de pays avec recherche par nom (thème clair/sombre).
 */
export function CountrySelect({ value, onChange, className }: CountrySelectProps) {
  const [open, setOpen] = useState(false);
  const [query, setQuery] = useState("");

  const displayValue = value || DEFAULT_COUNTRY;

  const filtered = useMemo(() => {
    const q = query.trim().toLowerCase();

    if (!q) {
      return COUNTRIES;
    }

    return COUNTRIES.filter((c) => c.name.toLowerCase().includes(q));
  }, [query]);

  /**
   * Sélectionne un pays et ferme la liste.
   */
  function selectCountry(name: string) {
    onChange(name);
    setQuery("");
    setOpen(false);
  }

  return (
    <div className={`relative ${className ?? ""}`}>
      <button
        type="button"
        className={triggerClass}
        onClick={() => setOpen((prev) => !prev)}
        aria-expanded={open}
        aria-haspopup="listbox"
      >
        {displayValue}
      </button>

      {open && (
        <div className="absolute z-30 mt-2 w-full overflow-hidden rounded-xl border border-line bg-bg-elev shadow-xl">
          <div className="border-b border-line p-2">
            <input
              type="search"
              placeholder="Rechercher un pays…"
              className="w-full rounded-lg border border-line bg-bg px-3 py-2 text-sm text-ink outline-none placeholder:text-muted focus:border-accent"
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              autoFocus
            />
          </div>
          <ul className="max-h-52 overflow-y-auto py-1" role="listbox">
            {filtered.length === 0 && (
              <li className="px-4 py-2 text-sm text-muted">Aucun pays trouvé</li>
            )}
            {filtered.map((country) => (
              <li key={country.code}>
                <button
                  type="button"
                  role="option"
                  aria-selected={country.name === displayValue}
                  className={`w-full px-4 py-2 text-left text-sm transition-colors hover:bg-accent-soft ${
                    country.name === displayValue
                      ? "bg-accent-soft font-medium text-ink"
                      : "text-ink-soft"
                  }`}
                  onClick={() => selectCountry(country.name)}
                >
                  {country.name}
                </button>
              </li>
            ))}
          </ul>
        </div>
      )}
    </div>
  );
}
