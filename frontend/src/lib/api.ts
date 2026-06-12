import { cache } from "react";
import type {
  ApiResponse,
  CheckPaymentStatusResponse,
  ContactPayload,
  PreRegistrationPayload,
  PreRegistrationResult,
  ProcessPaymentPayload,
  ProcessPaymentResponse,
  Project,
  ParticipantSpace,
  RegistrationPayload,
  RegistrationResult,
  SiteContent,
  TrainingSession,
} from "@/types/api";

const API_BASE =
  process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api";

/** Durée de cache ISR pour le contenu CMS (secondes). */
const REVALIDATE_SITE_SECONDS = 300;

/** Durée de cache ISR pour les sessions Academy (secondes). */
const REVALIDATE_SESSIONS_SECONDS = 60;

/**
 * Construit l'URL absolue d'un asset stocké côté Laravel (/storage/...).
 * Les chemins locaux Next.js (/images/...) restent relatifs.
 */
export function resolveStorageUrl(path?: string | null): string | null {
  if (!path) {
    return null;
  }

  const trimmed = path.trim();

  if (trimmed.startsWith("http://") || trimmed.startsWith("https://")) {
    return trimmed;
  }

  if (trimmed.startsWith("/images/")) {
    return trimmed;
  }

  const apiRoot = API_BASE.replace(/\/api\/?$/, "");

  if (trimmed.startsWith("/storage/") || trimmed.startsWith("storage/")) {
    const normalizedPath = trimmed.startsWith("/") ? trimmed : `/${trimmed}`;
    return `${apiRoot}${normalizedPath}`;
  }

  if (trimmed.startsWith("/assets/") || trimmed.startsWith("assets/")) {
    const normalizedPath = trimmed.startsWith("/") ? trimmed : `/${trimmed}`;
    return `${apiRoot}${normalizedPath}`;
  }

  if (!trimmed.startsWith("/")) {
    return `${apiRoot}/storage/${trimmed.replace(/^\/+/, "")}`;
  }

  return `${apiRoot}${trimmed}`;
}

/**
 * URL d'affiche session (API renvoie souvent une URL absolue).
 */
export function sessionCoverUrl(session: {
  cover_image?: string | null;
  cover_image_url?: string | null;
  pre_registration_cover_image_url?: string | null;
}, preferPreRegistration = false): string | null {
  if (preferPreRegistration && session.pre_registration_cover_image_url) {
    return resolveStorageUrl(session.pre_registration_cover_image_url);
  }

  return resolveStorageUrl(session.cover_image_url ?? session.cover_image);
}

/**
 * Récupère le contenu dynamique du site (à propos, services, compétences).
 */
export const getSiteContent = cache(async (): Promise<SiteContent | null> => {
  try {
    const response = await fetch(`${API_BASE}/site`, {
      next: { revalidate: REVALIDATE_SITE_SECONDS },
    });

    if (!response.ok) {
      return null;
    }

    const json: ApiResponse<SiteContent> = await response.json();
    return json.data ?? null;
  } catch {
    return null;
  }
});

/**
 * Récupère les projets portfolio depuis l'API.
 */
export const getProjects = cache(async (): Promise<Project[]> => {
  try {
    const response = await fetch(`${API_BASE}/project`, {
      next: { revalidate: 120 },
    });

    if (!response.ok) {
      return [];
    }

    const json: ApiResponse<Project[]> = await response.json();
    return json.data ?? [];
  } catch {
    return [];
  }
});

/**
 * Récupère les sessions Academy avec inscriptions ouvertes (status = open).
 */
export const getOpenSessions = cache(async (): Promise<TrainingSession[]> => {
  try {
    const response = await fetch(
      `${API_BASE}/academy/sessions?open_only=1`,
      { next: { revalidate: REVALIDATE_SESSIONS_SECONDS } },
    );

    if (!response.ok) {
      return [];
    }

    const json: ApiResponse<TrainingSession[]> = await response.json();
    return json.data ?? [];
  } catch {
    return [];
  }
});

/**
 * @deprecated Utiliser getOpenSessions() + pickPrimarySession()
 */
export async function getFeaturedSessions(): Promise<TrainingSession[]> {
  return getOpenSessions();
}

/**
 * Récupère une session Academy par slug.
 */
export const getSessionBySlug = cache(async (
  slug: string,
): Promise<TrainingSession | null> => {
  try {
    const response = await fetch(`${API_BASE}/academy/sessions/${slug}`, {
      next: { revalidate: REVALIDATE_SESSIONS_SECONDS },
    });

    if (!response.ok) {
      return null;
    }

    const json: ApiResponse<TrainingSession> = await response.json();
    return json.data ?? null;
  } catch {
    return null;
  }
});

/**
 * Envoie une inscription Academy (client-side).
 */
export async function submitRegistration(
  payload: RegistrationPayload
): Promise<ApiResponse<RegistrationResult>> {
  let response: Response;

  try {
    response = await fetch(`${API_BASE}/academy/register`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      body: JSON.stringify(payload),
    });
  } catch {
    return {
      success: false,
      message:
        "Impossible de joindre le serveur. Vérifiez votre connexion ou réessayez plus tard.",
      data: null as unknown as RegistrationResult,
    };
  }

  let json: ApiResponse<RegistrationResult> & {
    errors?: Record<string, string[]>;
  };

  try {
    json = await response.json();
  } catch {
    return {
      success: false,
      message: `Réponse serveur invalide (HTTP ${response.status}).`,
      data: null as unknown as RegistrationResult,
    };
  }

  if (!response.ok && json.success !== false) {
    json.success = false;
    json.message = json.message || `Erreur serveur (HTTP ${response.status}).`;
  }

  if (!json.success && json.errors) {
    const firstError = Object.values(json.errors).flat()[0];
    if (firstError) {
      json.message = firstError;
    }
  }

  return json;
}

/**
 * Envoie une pré-inscription Academy (client-side).
 */
export async function submitPreRegistration(
  payload: PreRegistrationPayload
): Promise<ApiResponse<PreRegistrationResult>> {
  let response: Response;

  try {
    response = await fetch(`${API_BASE}/academy/pre-register`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      body: JSON.stringify(payload),
    });
  } catch {
    return {
      success: false,
      message:
        "Impossible de joindre le serveur. Vérifiez votre connexion ou réessayez plus tard.",
      data: null as unknown as PreRegistrationResult,
    };
  }

  let json: ApiResponse<PreRegistrationResult> & {
    errors?: Record<string, string[]>;
  };

  try {
    json = await response.json();
  } catch {
    return {
      success: false,
      message: `Réponse serveur invalide (HTTP ${response.status}).`,
      data: null as unknown as PreRegistrationResult,
    };
  }

  if (!response.ok && json.success !== false) {
    json.success = false;
    json.message = json.message || `Erreur serveur (HTTP ${response.status}).`;
  }

  if (!json.success && json.errors) {
    const firstError = Object.values(json.errors).flat()[0];
    if (firstError) {
      json.message = firstError;
    }
  }

  return json;
}

/**
 * Lance le paiement (Mobile Money ou carte) pour une inscription.
 */
export async function processAcademyPayment(
  payload: ProcessPaymentPayload
): Promise<ApiResponse<ProcessPaymentResponse>> {
  const response = await fetch(`${API_BASE}/academy/payments/process`, {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),
  });

  return response.json();
}

/**
 * Vérifie le statut d'un paiement Mobile Money (polling).
 */
export async function checkAcademyPaymentStatus(
  reference: string
): Promise<ApiResponse<CheckPaymentStatusResponse>> {
  const response = await fetch(
    `${API_BASE}/academy/payments/check-status?reference=${encodeURIComponent(reference)}`,
    { headers: { Accept: "application/json" } }
  );

  return response.json();
}

/**
 * Charge l'espace participant via jeton d'accès.
 */
export async function getParticipantSpace(
  token: string
): Promise<ParticipantSpace | null> {
  try {
    const response = await fetch(`${API_BASE}/academy/participant/${token}`, {
      headers: { Accept: "application/json" },
      cache: "no-store",
    });

    if (!response.ok) {
      return null;
    }

    const json: ApiResponse<ParticipantSpace> = await response.json();
    return json.data ?? null;
  } catch {
    return null;
  }
}

/**
 * Enregistre l'acceptation de la notice de confidentialité.
 */
export async function acceptParticipantConfidentiality(
  token: string
): Promise<boolean> {
  try {
    const response = await fetch(
      `${API_BASE}/academy/participant/${token}/accept-confidentiality`,
      {
        method: "POST",
        headers: { Accept: "application/json" },
      }
    );

    return response.ok;
  } catch {
    return false;
  }
}

/**
 * URL front de l'espace participant.
 */
export function buildParticipantUrl(token: string): string {
  const site =
    process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/$/, "") ?? "http://localhost:3000";

  return `${site}/academy/espace/${token}`;
}

/**
 * Envoie le formulaire de contact (client-side).
 */
export async function submitContact(
  payload: ContactPayload
): Promise<ApiResponse<unknown>> {
  const response = await fetch(`${API_BASE}/contact`, {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),
  });

  return response.json();
}
