"use client";

import { useRef, useState } from "react";
import Image from "next/image";
import { Play, Pause } from "lucide-react";
import { Container } from "./Container";
import { Eyebrow } from "./Eyebrow";

export function VideoShowcase({
  eyebrow = "Coulisses",
  title,
  description,
  src,
  poster,
}: {
  eyebrow?: string;
  title: string;
  description?: string;
  src: string;
  poster: string;
}) {
  const videoRef = useRef<HTMLVideoElement>(null);
  const [playing, setPlaying] = useState(false);
  const [videoOk, setVideoOk] = useState(true);

  const toggle = () => {
    const v = videoRef.current;
    if (!v) return;
    if (v.paused) {
      void v.play();
      setPlaying(true);
    } else {
      v.pause();
      setPlaying(false);
    }
  };

  return (
    <section className="py-24 md:py-32">
      <Container>
        <div className="mx-auto max-w-3xl text-center">
          <Eyebrow>{eyebrow}</Eyebrow>
          <h2 className="font-display mt-4 text-4xl leading-[1.05] tracking-tight md:text-6xl">
            {title}
          </h2>
          {description && (
            <p className="mx-auto mt-5 max-w-2xl text-base text-muted md:text-lg">
              {description}
            </p>
          )}
        </div>

        <div className="mt-12 group relative aspect-video overflow-hidden rounded-[24px] border border-line bg-bg-elev">
          {videoOk ? (
            <video
              ref={videoRef}
              src={src}
              poster={poster}
              muted
              loop
              playsInline
              preload="metadata"
              onError={() => setVideoOk(false)}
              className="size-full object-cover"
            />
          ) : (
            <Image
              src={poster}
              alt={title}
              fill
              sizes="(min-width: 1024px) 1100px, 100vw"
              className="object-cover"
            />
          )}

          <div
            aria-hidden
            className={[
              "pointer-events-none absolute inset-0 transition-opacity duration-500",
              playing ? "opacity-0" : "opacity-100",
            ].join(" ")}
            style={{
              background:
                "linear-gradient(to top, rgba(10,10,10,0.55) 0%, rgba(10,10,10,0.10) 50%, transparent 100%)",
            }}
          />

          <button
            type="button"
            onClick={toggle}
            aria-label={playing ? "Mettre en pause" : "Lire la vidéo"}
            className="absolute inset-0 flex items-center justify-center"
          >
            <span
              className={[
                "surface-strong inline-flex size-20 items-center justify-center rounded-full shadow-2xl transition-transform duration-300 group-hover:scale-105 md:size-24",
                playing ? "opacity-0 group-hover:opacity-100" : "opacity-100",
              ].join(" ")}
            >
              {playing ? (
                <Pause className="size-7 md:size-8" />
              ) : (
                <Play className="ml-1 size-7 md:size-8" />
              )}
            </span>
          </button>

          <div className="pointer-events-none absolute inset-x-6 bottom-6 flex items-end justify-between text-white md:inset-x-8 md:bottom-8">
            <p className="font-display text-2xl tracking-tight md:text-3xl">
              {title}
            </p>
            <span className="font-mono text-[0.7rem] uppercase tracking-[0.2em] text-white/70">
              SDev Academy
            </span>
          </div>
        </div>
      </Container>
    </section>
  );
}
