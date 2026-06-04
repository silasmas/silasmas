"use client";

import { useMemo, useState } from "react";
import { COUNTRIES, DEFAULT_COUNTRY } from "@/data/countries";

interface CountrySelectProps {
  value: string;
  onChange: (value: string) => void;
  className?: string;
}

const inputClass =
  "w-full rounded-2xl border border-amber-500/15 bg-white/[0.03] px-4 py-3.5 text-white outline-none focus:border-amber-500/45";

/**
 * Liste déroulante de pays avec recherche par nom.
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
        className={`${inputClass} text-left`}
        onClick={() => setOpen((prev) => !prev)}
        aria-expanded={open}
        aria-haspopup="listbox"
      >
        {displayValue}
      </button>

      {open && (
        <div className="absolute z-30 mt-2 w-full rounded-2xl border border-amber-500/20 bg-[#0f1419] shadow-xl">
          <div className="p-2">
            <input
              type="search"
              placeholder="Rechercher un pays…"
              className="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none focus:border-amber-500/40"
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              autoFocus
            />
          </div>
          <ul
            className="max-h-52 overflow-y-auto py-1"
            role="listbox"
          >
            {filtered.length === 0 && (
              <li className="px-4 py-2 text-sm text-slate-500">Aucun pays trouvé</li>
            )}
            {filtered.map((country) => (
              <li key={country.code}>
                <button
                  type="button"
                  role="option"
                  className="w-full px-4 py-2 text-left text-sm text-slate-200 hover:bg-amber-500/15"
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
