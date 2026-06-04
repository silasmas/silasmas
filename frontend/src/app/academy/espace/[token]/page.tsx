import Link from "next/link";
import { ParticipantSpaceView } from "@/components/academy/ParticipantSpaceView";

interface ParticipantPageProps {
  params: Promise<{ token: string }>;
}

/**
 * Page espace participant (compte à rebours, profil, ressources).
 */
export default async function ParticipantPage({ params }: ParticipantPageProps) {
  const { token } = await params;

  return (
    <section className="section">
      <div className="container max-w-3xl">
        <Link
          href="/"
          className="mb-8 inline-flex text-sm text-slate-400 hover:text-amber-300"
        >
          ← Accueil
        </Link>
        <ParticipantSpaceView token={token} />
      </div>
    </section>
  );
}
