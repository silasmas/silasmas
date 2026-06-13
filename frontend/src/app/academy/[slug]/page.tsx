import type { Metadata } from "next";
import { Suspense } from "react";
import { AcademySessionLoader } from "@/components/sections/loaders/AcademySessionLoader";
import { AcademySessionPageSkeleton } from "@/components/skeleton/SectionSkeletons";
import { JsonLd } from "@/components/seo/JsonLd";
import { getSessionBySlug } from "@/lib/api";
import { richTextExcerpt } from "@/lib/rich-html";
import { buildCourseJsonLd, buildPageMetadata } from "@/lib/seo";

interface AcademyPageProps {
  params: Promise<{ slug: string }>;
}

/**
 * Métadonnées SEO dynamiques par session Academy.
 */
export async function generateMetadata({
  params,
}: AcademyPageProps): Promise<Metadata> {
  const { slug } = await params;
  const session = await getSessionBySlug(slug);

  if (!session) {
    return { title: "Session introuvable" };
  }

  const description =
    session.subtitle
    ?? richTextExcerpt(session.description, 160)
    ?? `Inscription à la formation ${session.title} — SDev Academy.`;

  return buildPageMetadata({
    title: session.title,
    description,
    path: `/academy/${slug}`,
    image: session.cover_image_url ?? session.cover_image,
  });
}

/**
 * Contenu async de la page session (params await).
 */
async function AcademySessionPageContent({ params }: AcademyPageProps) {
  const { slug } = await params;
  const session = await getSessionBySlug(slug);
  const courseJsonLd = session ? buildCourseJsonLd(session) : null;

  return (
    <>
      {courseJsonLd && <JsonLd data={courseJsonLd} />}
      <AcademySessionLoader slug={slug} />
    </>
  );
}

/**
 * Page détail session Academy avec skeleton Suspense.
 */
export default function AcademySessionPage({ params }: AcademyPageProps) {
  return (
    <Suspense fallback={<AcademySessionPageSkeleton />}>
      <AcademySessionPageContent params={params} />
    </Suspense>
  );
}
