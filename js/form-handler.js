// js/form-handler.js

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("contactForm");
    if (!form) return;
  
    const submitButton = form.querySelector(".btn-submit");
    const btnText = submitButton?.querySelector(".btn-text");
    const btnLoading = submitButton?.querySelector(".btn-loading");
  
    const successBox = document.getElementById("formSuccess");
    const errorBox = document.getElementById("formError");
  
    const nomeEl = document.getElementById("nome");
    const emailEl = document.getElementById("email");
    const telEl = document.getElementById("telefone");
    const localidadeEl = document.getElementById("localidade");
    const mensagemEl = document.getElementById("mensagem");
  
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
  
    function setLoading(isLoading) {
      if (!submitButton) return;
      submitButton.disabled = isLoading;
      if (btnText) btnText.style.display = isLoading ? "none" : "inline";
      if (btnLoading) btnLoading.style.display = isLoading ? "inline" : "none";
    }
  
    // ✅ 1) Telefone: bloqueia letras enquanto escreve
    if (telEl) {
      telEl.addEventListener("input", () => {
        // deixa apenas números, espaços e +
        telEl.value = telEl.value.replace(/[^\d+ ]/g, "");
  
        // garante que só existe um "+" e apenas no início
        if (telEl.value.includes("+")) {
          telEl.value =
            (telEl.value.startsWith("+") ? "+" : "") +
            telEl.value.replace(/\+/g, "").trim();
        }
  
        // remove erro custom quando o user corrige
        telEl.setCustomValidity("");
      });
  
      // Mensagem PT-PT quando falha pattern/required
      telEl.addEventListener("invalid", () => {
        if (telEl.validity.valueMissing) {
          telEl.setCustomValidity("Introduza o seu número de telefone.");
        } else if (telEl.validity.patternMismatch) {
          telEl.setCustomValidity("Telefone inválido. Use apenas números (pode incluir +351) e espaços.");
        } else {
          telEl.setCustomValidity("Telefone inválido.");
        }
      });
    }
  
    // ✅ 2) Email: mensagem PT-PT
    if (emailEl) {
      emailEl.addEventListener("input", () => emailEl.setCustomValidity(""));
      emailEl.addEventListener("invalid", () => {
        if (emailEl.validity.valueMissing) {
          emailEl.setCustomValidity("Introduza o seu email.");
        } else if (emailEl.validity.typeMismatch) {
          emailEl.setCustomValidity("Email inválido. Exemplo: nome@email.com");
        } else {
          emailEl.setCustomValidity("Email inválido.");
        }
      });
    }
  
    // ✅ 3) Outros campos: mensagens simples (opcional mas profissional)
    const requiredFields = [nomeEl, localidadeEl, mensagemEl].filter(Boolean);
    requiredFields.forEach((el) => {
      el.addEventListener("input", () => el.setCustomValidity(""));
      el.addEventListener("invalid", () => {
        if (el.validity.valueMissing) el.setCustomValidity("Este campo é obrigatório.");
      });
    });
  
    // ✅ Submit
    form.addEventListener("submit", async (e) => {
      e.preventDefault();
  
      // deixa o browser validar (required, email, pattern do telefone, etc.)
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }
  
      const nome = nomeEl?.value.trim() || "";
      const email = emailEl?.value.trim() || "";
      const telefone = telEl?.value.trim() || "";
      const localidade = localidadeEl?.value.trim() || "";
      const mensagem = mensagemEl?.value.trim() || "";
      const botField = form.querySelector('input[name="botField"]')?.value || "";
  
      // Segurança extra (mesmo com validação do browser)
      if (!nome || !email || !telefone || !localidade || !mensagem) {
        showError("Preenche todos os campos obrigatórios.");
        return;
      }
  
      // UI
      if (errorBox) errorBox.style.display = "none";
      if (successBox) successBox.style.display = "none";
      setLoading(true);
  
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
        setLoading(false);
      }
    });
  });
  