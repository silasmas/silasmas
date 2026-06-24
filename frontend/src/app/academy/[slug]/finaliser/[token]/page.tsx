import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { PaymentFinalizeView } from "@/components/academy/PaymentFinalizeView";
import { getSessionBySlug } from "@/lib/api";
import { buildPageMetadata } from "@/lib/seo";

interface PaymentFinalizePageProps {
  params: Promise<{
    slug: string;
    token: string;
  }>;
}

/**
 * Métadonnées SEO — page de finalisation paiement.
 */
export async function generateMetadata({
  params,
}: PaymentFinalizePageProps): Promise<Metadata> {
  const { slug } = await params;
  const session = await getSessionBySlug(slug);

  if (!session) {
    return { title: "Session introuvable" };
  }

  return buildPageMetadata({
    title: `Finaliser mon inscription — ${session.title}`,
    description: `Finalisez votre paiement pour confirmer votre place à la formation ${session.title}.`,
    path: `/academy/${slug}/finaliser`,
    noIndex: true,
  });
}

/**
 * Page dédiée : récapitulatif + paiement (liens e-mail).
 */
export default async function AcademyPaymentFinalizePage({
  params,
}: PaymentFinalizePageProps) {
  const { slug, token } = await params;
  const session = await getSessionBySlug(slug);

  if (!session) {
    notFound();
  }

  return <PaymentFinalizeView session={session} accessToken={token} />;
}
