document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("contactForm");
  if (!form) return;

  const submitButton = form.querySelector(".btn-submit");
  const btnText = submitButton?.querySelector(".btn-text");
  const btnLoading = submitButton?.querySelector(".btn-loading");

  const successBox = document.getElementById("formSuccess");
  const errorBox = document.getElementById("formError");

  function showError(msg) {
    if (errorBox) {
      errorBox.style.display = "block";
      const p = errorBox.querySelector("p");
      if (p) p.textContent = msg;
    }
    if (successBox) successBox.style.display = "none";
  }

  function showSuccess() {
    if (successBox) successBox.style.display = "block";
    if (errorBox) errorBox.style.display = "none";
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const nome = document.getElementById("nome")?.value.trim();
    const email = document.getElementById("email")?.value.trim();
    const telefone = document.getElementById("telefone")?.value.trim();
    const localidade = document.getElementById("localidade")?.value.trim();
    const mensagem = document.getElementById("mensagem")?.value.trim();
    const botField = form.querySelector('input[name="botField"]')?.value || "";

    if (!nome || !email || !telefone || !localidade || !mensagem) {
      showError("Preenche todos os campos obrigatórios.");
      return;
    }

    if (submitButton) submitButton.disabled = true;
    if (btnText) btnText.style.display = "none";
    if (btnLoading) btnLoading.style.display = "inline";
    if (errorBox) errorBox.style.display = "none";
    if (successBox) successBox.style.display = "none";

    try {
      const res = await fetch("/.netlify/functions/contact", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ nome, email, telefone, localidade, mensagem, botField }),
      });

      const out = await res.json().catch(() => ({}));
      if (!res.ok || !out.ok) throw new Error(out.error || "Erro ao enviar.");

      showSuccess();
      form.reset();
    } catch (err) {
      console.error(err);
      showError("Ocorreu um erro ao enviar. Tenta novamente ou contacta por telefone.");
    } finally {
      if (submitButton) submitButton.disabled = false;
      if (btnText) btnText.style.display = "inline";
      if (btnLoading) btnLoading.style.display = "none";
    }
  });
});
