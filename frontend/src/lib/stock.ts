/**
 * Stock visuals
 * - Photos: Unsplash (free, hotlink-friendly, OK for commercial use).
 * - Video: Pexels (free, attribution appreciated).
 *
 * These are picked deliberately to fit the editorial / warm palette.
 * Replace any of them with real client visuals later.
 */

const u = (id: string, w = 1600) =>
  `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${w}&q=80`;

export const stock = {
  hero: {
    portrait: u("photo-1531123897727-8f129e1688ce", 1400),
    workspace: u("photo-1499951360447-b19be8fe80f5", 1600),
  },
  silas: {
    portrait: "/images/silas/silas-portrait.png",
    speaking: "/images/silas/silas-formel.png",
  },
  agence: {
    workshop: u("photo-1517048676732-d65bc937f952", 1600),
    code: u("photo-1498050108023-c5249f4df085", 1600),
    design: u("photo-1561070791-2526d30994b8", 1600),
  },
  academy: {
    classroom: u("photo-1524178232363-1fb2b075b655", 1600),
    students: u("photo-1523240795612-9a054b0db644", 1600),
    mentoring: u("photo-1522202176988-66273c2fd55f", 1600),
    poster: u("photo-1496181133206-80ce9b88a853", 1600),
  },
  // Pexels: free CC0-style license, hotlinkable from videos.pexels.com
  video: {
    src: "https://videos.pexels.com/video-files/3209828/3209828-uhd_2560_1440_25fps.mp4",
    poster: u("photo-1496181133206-80ce9b88a853", 1600),
  },
  projects: {
    "pla-cabinet": u("photo-1521737604893-d14cc237f11d", 1400),
    "agr-rdc": u("photo-1551650975-87deedd944c3", 1400),
    "jp-tshienda": u("photo-1591115765373-5207764f72e7", 1400),
    skillup: u("photo-1551434678-e076c223a692", 1400),
    "action-damien": u("photo-1532938911079-1b06ac7ceec7", 1400),
    adorons: u("photo-1493225457124-a3eb161ffa5f", 1400),
  },
} as const;

export type StockKey = keyof typeof stock;
