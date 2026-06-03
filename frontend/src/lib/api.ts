import type {
  ApiResponse,
  ContactPayload,
  Project,
  RegistrationPayload,
  TrainingSession,
} from "@/types/api";

const API_BASE =
  process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api";

/**
 * Construit l'URL absolue d'un asset stocké côté Laravel (/storage/...).
 * Les chemins locaux Next.js (/images/...) restent relatifs.
 */
export function resolveStorageUrl(path?: string | null): string | null {
  if (!path) {
    return null;
  }

  if (path.startsWith("http")) {
    return path;
  }

  if (path.startsWith("/images/")) {
    return path;
  }

  if (path.startsWith("/storage/") || path.startsWith("storage/")) {
    const apiRoot = API_BASE.replace(/\/api\/?$/, "");
    const normalizedPath = path.startsWith("/") ? path : `/${path}`;
    return `${apiRoot}${normalizedPath}`;
  }

  return path.startsWith("/") ? path : `/${path}`;
}

/**
 * Récupère les projets portfolio depuis l'API.
 */
export async function getProjects(): Promise<Project[]> {
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
}

/**
 * Récupère les sessions Academy mises en avant.
 */
export async function getFeaturedSessions(): Promise<TrainingSession[]> {
  try {
    const response = await fetch(
      `${API_BASE}/academy/sessions?featured_only=1&open_only=1`,
      { next: { revalidate: 60 } }
    );

    if (!response.ok) {
      return [];
    }

    const json: ApiResponse<TrainingSession[]> = await response.json();
    return json.data ?? [];
  } catch {
    return [];
  }
}

/**
 * Récupère une session Academy par slug.
 */
export async function getSessionBySlug(
  slug: string
): Promise<TrainingSession | null> {
  try {
    const response = await fetch(`${API_BASE}/academy/sessions/${slug}`, {
      next: { revalidate: 60 },
    });

    if (!response.ok) {
      return null;
    }

    const json: ApiResponse<TrainingSession> = await response.json();
    return json.data ?? null;
  } catch {
    return null;
  }
}

/**
 * Envoie une inscription Academy (client-side).
 */
export async function submitRegistration(
  payload: RegistrationPayload
): Promise<ApiResponse<unknown>> {
  const response = await fetch(`${API_BASE}/academy/register`, {
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
