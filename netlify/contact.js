// netlify/functions/contact.js

const json = (statusCode, bodyObj) => ({
  statusCode,
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(bodyObj),
});

exports.handler = async (event) => {
  try {
    if (event.httpMethod !== "POST") {
      return json(405, { ok: false, error: "Method Not Allowed" });
    }

    const payload = JSON.parse(event.body || "{}");
    const clean = (v, max) => String(v ?? "").trim().slice(0, max);

    const data = {
      nome: clean(payload.nome, 120),
      email: clean(payload.email, 160),
      telefone: clean(payload.telefone, 40),
      localidade: clean(payload.localidade, 120),
      mensagem: clean(payload.mensagem, 4000),
      ip: clean(event.headers["x-nf-client-connection-ip"] || "", 80),
      user_agent: clean(event.headers["user-agent"] || "", 300),
    };

    // Honeypot anti-bot
    const botField = clean(payload.botField, 200);
    if (botField) return json(200, { ok: true });

    if (!data.nome || !data.email || !data.telefone || !data.localidade || !data.mensagem) {
      return json(400, { ok: false, error: "Campos obrigatórios em falta." });
    }

    // ✅ NÃO metes valores aqui. Vêm do Netlify (Environment Variables)
    const SUPABASE_URL = process.env.SUPABASE_URL;
    const SUPABASE_SECRET_KEY = process.env.SUPABASE_SECRET_KEY;

    if (!SUPABASE_URL || !SUPABASE_SECRET_KEY) {
      return json(500, { ok: false, error: "Env vars em falta no Netlify." });
    }

    const insertRes = await fetch(`${SUPABASE_URL}/rest/v1/contact_messages`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        apikey: SUPABASE_SECRET_KEY,
        Authorization: `Bearer ${SUPABASE_SECRET_KEY}`,
        Prefer: "return=minimal",
      },
      body: JSON.stringify([data]),
    });

    if (!insertRes.ok) {
      const t = await insertRes.text();
      return json(500, { ok: false, error: "Erro ao gravar na BD.", detail: t });
    }

    return json(200, { ok: true });
  } catch {
    return json(500, { ok: false, error: "Erro inesperado." });
  }
};
