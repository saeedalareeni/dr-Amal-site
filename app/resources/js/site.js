import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

const loader = document.getElementById("loader");
      const progress = document.getElementById("progress");

      window.addEventListener("load", () => {
        loader.style.opacity = "0";
        setTimeout(() => loader.remove(), 650);
      });

      const nav = document.querySelector(".nav");
      const navToggle = document.querySelector(".nav-toggle");
      const navLinks = document.querySelectorAll(".nav-links a");

      navToggle.addEventListener("click", () => {
        const isOpen = nav.classList.toggle("is-open");
        navToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
        navToggle.innerHTML = isOpen
          ? '<i class="fa-solid fa-xmark"></i>'
          : '<i class="fa-solid fa-bars"></i>';
      });

      navLinks.forEach((link) => {
        link.addEventListener("click", () => {
          nav.classList.remove("is-open");
          navToggle.setAttribute("aria-expanded", "false");
          navToggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
        });
      });

      const projectForm = document.querySelector(".project-form");
      const consultationDate = document.getElementById("consultation-date");
      const formMessage = projectForm.querySelector(".form-message");

      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const todayValue = [
        today.getFullYear(),
        String(today.getMonth() + 1).padStart(2, "0"),
        String(today.getDate()).padStart(2, "0"),
      ].join("-");
      consultationDate.min = todayValue;

      function fieldErrorText(input) {
        if (input.validity.valueMissing) {
          return "هذا الحقل مطلوب.";
        }
        if (input.type === "email" && input.validity.typeMismatch) {
          return "اكتب بريد إلكتروني صحيح.";
        }
        if (input.validity.customError) {
          return input.validationMessage;
        }
        if (input.validity.tooShort) {
          return `اكتب على الأقل ${input.minLength} أحرف.`;
        }
        if (
          input.type === "date" &&
          (input.validity.rangeUnderflow || input.value)
        ) {
          const selectedDate = new Date(`${input.value}T00:00:00`);
          if (selectedDate < today) return "اختاري تاريخ اليوم أو تاريخ قادم.";
        }
        return "";
      }

      function validateField(input) {
        const field = input.closest(".field");
        const error = field.querySelector(".field-error");
        const message = fieldErrorText(input);
        field.classList.toggle("is-invalid", Boolean(message));
        error.textContent = message;
        return !message;
      }

      const blockedNewsletterDomains = new Set([
        "example.com",
        "example.net",
        "example.org",
        "test.com",
        "fake.com",
        "invalid.com",
        "mailinator.com",
        "10minutemail.com",
        "tempmail.com",
        "temp-mail.org",
        "yopmail.com",
        "guerrillamail.com",
        "sharklasers.com",
        "getnada.com",
        "throwawaymail.com",
      ]);

      function fakeEmailMessage(input) {
        const email = input.value.trim().toLowerCase();
        const [localPart = "", domain = ""] = email.split("@");

        if (blockedNewsletterDomains.has(domain)) {
          return "استخدم بريد حقيقي، لا يمكن قبول الإيميلات التجريبية أو المؤقتة.";
        }

        if (
          /^(test|fake|demo|sample|email|name|user|admin|qwerty|asdf)\d*$/.test(
            localPart,
          )
        ) {
          return "استخدم بريدك الحقيقي حتى تظهر روابط التحميل.";
        }

        return "";
      }

      function validateNewsletterEmail(input) {
        input.setCustomValidity("");

        if (!validateField(input)) {
          return false;
        }

        const message = fakeEmailMessage(input);

        if (message) {
          input.setCustomValidity(message);
          validateField(input);
          return false;
        }

        return true;
      }

      projectForm
        .querySelectorAll("input, select, textarea")
        .forEach((input) => {
          input.addEventListener("blur", () => validateField(input));
          input.addEventListener("input", () => {
            if (input.closest(".field").classList.contains("is-invalid")) {
              validateField(input);
            }
          });
        });

      projectForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        formMessage.classList.remove("is-visible");
        const fields = [
          ...projectForm.querySelectorAll("input, select, textarea"),
        ];
        const isValid = fields.map(validateField).every(Boolean);

        if (!isValid) {
          const firstInvalid = projectForm.querySelector(
            ".field.is-invalid input, .field.is-invalid select, .field.is-invalid textarea",
          );
          firstInvalid?.focus();
          return;
        }

        const button = projectForm.querySelector("button[type='submit']");
        button.disabled = true;
        formMessage.classList.remove("is-error");
        try {
          const response = await fetch(projectForm.action, {
            method: "POST",
            body: new FormData(projectForm),
            headers: { Accept: "application/json" },
          });
          const payload = await response.json();
          if (!response.ok) throw new Error(payload.message || Object.values(payload.errors || {})[0]?.[0] || "تعذر إرسال الطلب.");
          formMessage.querySelector("strong").textContent = payload.message;
          formMessage.classList.add("is-visible");
          projectForm.reset();
          projectForm.querySelector("[name='form_started']").value = Math.floor(Date.now() / 1000);
          consultationDate.min = todayValue;
          formMessage.scrollIntoView({ behavior: "smooth", block: "nearest" });
        } catch (error) {
          formMessage.querySelector("strong").textContent = error.message;
          formMessage.classList.add("is-visible", "is-error");
        } finally {
          button.disabled = false;
        }
      });

      const newsletterForm = document.querySelector(".newsletter-form");

      if (newsletterForm) {
        const newsletterMessage = newsletterForm.querySelector(".form-message");
        const newsletterEmail = newsletterForm.querySelector(
          "input[type='email']",
        );
        const newsletterDownloads = document.querySelector(
          ".newsletter-downloads",
        );

        newsletterEmail.addEventListener("blur", () =>
          validateNewsletterEmail(newsletterEmail),
        );
        newsletterEmail.addEventListener("input", () => {
          newsletterEmail.setCustomValidity("");
          newsletterMessage.classList.remove("is-visible");
          newsletterDownloads?.classList.remove("is-visible");
          if (
            newsletterEmail.closest(".field").classList.contains("is-invalid")
          ) {
            validateField(newsletterEmail);
          }
        });

        newsletterForm.addEventListener("submit", async (event) => {
          event.preventDefault();
          newsletterMessage.classList.remove("is-visible");
          newsletterDownloads?.classList.remove("is-visible");

          if (!validateNewsletterEmail(newsletterEmail)) {
            newsletterEmail.focus();
            return;
          }

          const button = newsletterForm.querySelector("button[type='submit']");
          button.disabled = true;
          newsletterMessage.classList.remove("is-error");
          try {
            const response = await fetch(newsletterForm.action, {
              method: "POST",
              body: new FormData(newsletterForm),
              headers: { Accept: "application/json" },
            });
            const payload = await response.json();
            if (!response.ok) throw new Error(payload.message || Object.values(payload.errors || {})[0]?.[0] || "تعذر إرسال رابط التحقق.");
            newsletterMessage.querySelector("strong").textContent = payload.message;
            newsletterMessage.classList.add("is-visible");
            newsletterForm.reset();
            newsletterForm.querySelector("[name='form_started']").value = Math.floor(Date.now() / 1000);
          } catch (error) {
            newsletterMessage.querySelector("strong").textContent = error.message;
            newsletterMessage.classList.add("is-visible", "is-error");
          } finally {
            button.disabled = false;
          }
        });
      }

      const heroCanvas = document.getElementById("hero-web");
      const heroCtx = heroCanvas.getContext("2d");
      const webNodes = [];
      const nodeCount = 38;

      function resizeHeroCanvas() {
        const ratio = Math.min(window.devicePixelRatio || 1, 2);
        const rect = heroCanvas.parentElement.getBoundingClientRect();
        heroCanvas.width = rect.width * ratio;
        heroCanvas.height = rect.height * ratio;
        heroCanvas.style.width = `${rect.width}px`;
        heroCanvas.style.height = `${rect.height}px`;
        heroCtx.setTransform(ratio, 0, 0, ratio, 0, 0);
        buildHeroWeb(rect.width, rect.height);
      }

      function buildHeroWeb(width, height) {
        webNodes.length = 0;
        for (let i = 0; i < nodeCount; i++) {
          webNodes.push({
            x: Math.random() * width,
            y: Math.random() * height,
            baseX: Math.random() * width,
            baseY: Math.random() * height,
            phase: Math.random() * Math.PI * 2,
          });
        }
      }

      function drawHeroWeb() {
        requestAnimationFrame(drawHeroWeb);
        const width = heroCanvas.clientWidth;
        const height = heroCanvas.clientHeight;
        const time = performance.now() * 0.0007;

        heroCtx.clearRect(0, 0, width, height);

        webNodes.forEach((node) => {
          node.x = node.baseX + Math.sin(time + node.phase) * 11;
          node.y = node.baseY + Math.cos(time * 0.9 + node.phase) * 9;
        });

        heroCtx.lineWidth = 1;
        heroCtx.strokeStyle = "rgba(6, 78, 59, 0.13)";
        heroCtx.fillStyle = "rgba(180, 83, 9, 0.22)";

        webNodes.forEach((a, i) => {
          for (let j = i + 1; j < webNodes.length; j++) {
            const b = webNodes[j];
            const dx = a.x - b.x;
            const dy = a.y - b.y;
            const distance = Math.hypot(dx, dy);
            if (distance < 150) {
              heroCtx.globalAlpha = (1 - distance / 150) * 0.46;
              heroCtx.beginPath();
              heroCtx.moveTo(a.x, a.y);
              heroCtx.lineTo(b.x, b.y);
              heroCtx.stroke();
            }
          }
        });

        heroCtx.globalAlpha = 0.55;
        webNodes.forEach((node) => {
          heroCtx.beginPath();
          heroCtx.arc(node.x, node.y, 1.6, 0, Math.PI * 2);
          heroCtx.fill();
        });
      }

      window.addEventListener("resize", () => {
        resizeHeroCanvas();
      });

      resizeHeroCanvas();
      drawHeroWeb();

      const hero = document.querySelector(".hero");
      const kineticWords = [...document.querySelectorAll(".kinetic-word")];
      const reducedMotionQuery = window.matchMedia(
        "(prefers-reduced-motion: reduce)",
      );

      if (hero && kineticWords.length && !reducedMotionQuery.matches) {
        const pointerState = {
          x: 0,
          y: 0,
          targetX: 0,
          targetY: 0,
        };
        let kineticFrame = null;

        function renderKineticWords() {
          pointerState.x += (pointerState.targetX - pointerState.x) * 0.12;
          pointerState.y += (pointerState.targetY - pointerState.y) * 0.12;

          kineticWords.forEach((word) => {
            const depth = Number(word.dataset.depth || 36);
            const spin = Number(word.dataset.spin || 4);
            const x = pointerState.x * depth;
            const y = pointerState.y * depth;
            const rotate = pointerState.x * spin;
            word.style.transform = `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, 0) rotate(${rotate.toFixed(2)}deg)`;
          });

          const settled =
            Math.abs(pointerState.targetX - pointerState.x) < 0.002 &&
            Math.abs(pointerState.targetY - pointerState.y) < 0.002;

          if (settled) {
            kineticFrame = null;
            return;
          }

          kineticFrame = requestAnimationFrame(renderKineticWords);
        }

        function scheduleKineticWords() {
          if (kineticFrame === null) {
            kineticFrame = requestAnimationFrame(renderKineticWords);
          }
        }

        hero.addEventListener(
          "pointermove",
          (event) => {
            if (event.pointerType === "touch") return;
            const rect = hero.getBoundingClientRect();
            const normalizedX = (event.clientX - rect.left) / rect.width - 0.5;
            const normalizedY = (event.clientY - rect.top) / rect.height - 0.5;
            pointerState.targetX = normalizedX * -2;
            pointerState.targetY = normalizedY * -2;
            scheduleKineticWords();
          },
          { passive: true },
        );

        hero.addEventListener("pointerleave", () => {
          pointerState.targetX = 0;
          pointerState.targetY = 0;
          scheduleKineticWords();
        });
      }

      gsap.registerPlugin(ScrollTrigger);

      gsap.utils.toArray(".reveal").forEach((item, index) => {
        gsap.to(item, {
          opacity: 1,
          y: 0,
          duration: 0.9,
          ease: "power3.out",
          delay: (index % 4) * 0.04,
          scrollTrigger: {
            trigger: item,
            start: "top 86%",
          },
        });
      });

      document.querySelectorAll("[data-stat-target]").forEach((number) => {
        const target = Number(number.dataset.statTarget || 0);
        if (!Number.isFinite(target) || reducedMotionQuery.matches) return;

        const counter = { value: 0 };
        gsap.to(counter, {
          value: target,
          duration: 1.45,
          ease: "power2.out",
          onStart: () => {
            number.textContent = "0";
          },
          onUpdate: () => {
            number.textContent = String(Math.round(counter.value));
          },
          onComplete: () => {
            number.textContent = String(target);
          },
          scrollTrigger: {
            trigger: number.closest(".impact-stats"),
            start: "top 82%",
            once: true,
          },
        });
      });

      const storeLab = document.querySelector("[data-store-lab]");

      if (storeLab) {
        const storeImage = storeLab.querySelector("[data-store-image]");
        const storeScreen = storeLab.querySelector("[data-store-screen]");
        const storeIndex = storeLab.querySelector("[data-store-index]");
        const storeCategory = storeLab.querySelector("[data-store-category]");
        const storeTitle = storeLab.querySelector("[data-store-title]");
        const storeDescription = storeLab.querySelector(
          "[data-store-description]",
        );
        const storeChips = storeLab.querySelector("[data-store-chips]");
        const storeUrl = storeLab.querySelector("[data-store-url]");
        const storePicks = [...storeLab.querySelectorAll("[data-store-pick]")];
        let activeStoreIndex = 0;
        let storeAutoTimer = null;
        let storeResumeTimer = null;

        function setActiveStore(index) {
          const nextIndex = Math.max(0, Math.min(storePicks.length - 1, index));
          const pick = storePicks[nextIndex];
          if (
            !pick ||
            (nextIndex === activeStoreIndex &&
              pick.classList.contains("is-active"))
          )
            return;

          activeStoreIndex = nextIndex;
          storePicks.forEach((item, itemIndex) => {
            const isActive = itemIndex === nextIndex;
            item.classList.toggle("is-active", isActive);
            if (isActive) {
              item.setAttribute("aria-current", "true");
            } else {
              item.removeAttribute("aria-current");
            }
          });

          storeScreen?.classList.add("is-swapping");

          setTimeout(
            () => {
              storeImage.src = pick.dataset.src;
              storeImage.alt = pick.dataset.title || "متجر إلكتروني";
              storeTitle.textContent = pick.dataset.title || "";
              storeCategory.textContent = pick.dataset.category || "";
              storeDescription.textContent = pick.dataset.description || "";
              storeUrl.textContent = pick.dataset.url || "";
              storeIndex.textContent = `${String(nextIndex + 1).padStart(2, "0")} / ${String(storePicks.length).padStart(2, "0")}`;

              storeChips.innerHTML = "";
              (pick.dataset.chips || "")
                .split("|")
                .filter(Boolean)
                .forEach((chip) => {
                  const item = document.createElement("span");
                  item.textContent = chip;
                  storeChips.appendChild(item);
                });

              storeScreen?.classList.remove("is-swapping");

              if (!reducedMotionQuery.matches) {
                gsap.fromTo(
                  storeImage,
                  { y: 16, scale: 0.985, opacity: 0.5 },
                  {
                    y: 0,
                    scale: 1,
                    opacity: 1,
                    duration: 0.48,
                    ease: "power3.out",
                    overwrite: true,
                  },
                );
              }
            },
            reducedMotionQuery.matches ? 0 : 180,
          );
        }

        storePicks.forEach((pick, index) => {
          pick.addEventListener("click", () => {
            pauseStoreAuto();
            setActiveStore(index);
          });
        });

        function startStoreAuto() {
          clearInterval(storeAutoTimer);
          if (storePicks.length < 2) return;
          storeAutoTimer = setInterval(() => {
            setActiveStore((activeStoreIndex + 1) % storePicks.length);
          }, 4200);
        }

        function pauseStoreAuto() {
          clearInterval(storeAutoTimer);
          clearTimeout(storeResumeTimer);
          storeResumeTimer = setTimeout(startStoreAuto, 7600);
        }

        storeLab.addEventListener("mouseenter", () => {
          clearInterval(storeAutoTimer);
          clearTimeout(storeResumeTimer);
        });

        storeLab.addEventListener("mouseleave", startStoreAuto);

        document.addEventListener("visibilitychange", () => {
          if (document.hidden) {
            clearInterval(storeAutoTimer);
            clearTimeout(storeResumeTimer);
          } else {
            startStoreAuto();
          }
        });

        startStoreAuto();
      }

      gsap.utils
        .toArray(
          ".result-case, .social-slide, .testimonial-card, .service-card, .download-card",
        )
        .forEach((card) => {
          card.addEventListener("mousemove", (event) => {
            const rect = card.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;
            gsap.to(card, {
              rotateY: x * -6,
              rotateX: y * 5,
              y: -5,
              duration: 0.35,
              ease: "power2.out",
            });
          });
          card.addEventListener("mouseleave", () => {
            gsap.to(card, {
              rotateY: 0,
              rotateX: 0,
              y: 0,
              duration: 0.45,
              ease: "power2.out",
            });
          });
        });

      document.querySelectorAll(".gallery-shell").forEach((shell) => {
        const track = shell.querySelector(".gallery-track");
        const prev = shell.querySelector(".gallery-nav.prev");
        const next = shell.querySelector(".gallery-nav.next");
        let isDragging = false;
        let dragStarted = false;
        let startX = 0;
        let startScrollLeft = 0;
        let autoSlideTimer = null;
        let resumeTimer = null;
        let suppressClickUntil = 0;

        function scrollAmount() {
          const card = track.firstElementChild;
          if (!card) return track.clientWidth * 0.8;
          const gap = parseFloat(getComputedStyle(track).gap) || 16;
          return card.getBoundingClientRect().width + gap;
        }

        function updateButtons() {
          const max = track.scrollWidth - track.clientWidth - 2;
          prev.disabled = track.scrollLeft <= 2;
          next.disabled = track.scrollLeft >= max;
        }

        function pauseAutoSlide() {
          clearInterval(autoSlideTimer);
          clearTimeout(resumeTimer);
          resumeTimer = setTimeout(startAutoSlide, 4500);
        }

        function autoSlide() {
          const max = track.scrollWidth - track.clientWidth - 2;
          if (max <= 2) return;

          if (track.scrollLeft >= max) {
            track.scrollTo({ left: 0, behavior: "smooth" });
            return;
          }

          track.scrollBy({ left: scrollAmount(), behavior: "smooth" });
        }

        function startAutoSlide() {
          clearInterval(autoSlideTimer);
          if (reducedMotionQuery.matches) return;
          autoSlideTimer = setInterval(autoSlide, 3200);
        }

        prev.addEventListener("click", () => {
          pauseAutoSlide();
          track.scrollBy({ left: -scrollAmount(), behavior: "smooth" });
        });

        next.addEventListener("click", () => {
          pauseAutoSlide();
          track.scrollBy({ left: scrollAmount(), behavior: "smooth" });
        });

        track.addEventListener("scroll", updateButtons, { passive: true });
        track.addEventListener("keydown", (event) => {
          if (event.key !== "ArrowLeft" && event.key !== "ArrowRight") return;
          event.preventDefault();
          pauseAutoSlide();
          const direction = event.key === "ArrowLeft" ? 1 : -1;
          track.scrollBy({
            left: scrollAmount() * direction,
            behavior: "smooth",
          });
        });
        window.addEventListener("resize", updateButtons);
        shell.addEventListener("mouseenter", () => {
          clearInterval(autoSlideTimer);
          clearTimeout(resumeTimer);
        });
        shell.addEventListener("mouseleave", startAutoSlide);

        track.addEventListener("pointerdown", (event) => {
          if (event.button !== 0) return;
          pauseAutoSlide();
          isDragging = true;
          dragStarted = false;
          track.dataset.dragged = "false";
          startX = event.clientX;
          startScrollLeft = track.scrollLeft;
          track.classList.add("is-dragging");
        });

        track.addEventListener("pointermove", (event) => {
          if (!isDragging) return;
          const delta = event.clientX - startX;
          if (Math.abs(delta) > 10 && !dragStarted) {
            dragStarted = true;
            track.dataset.dragged = "true";
            track.setPointerCapture(event.pointerId);
          }
          if (dragStarted) {
            track.scrollLeft = startScrollLeft - delta;
          }
        });

        function endDrag(event) {
          if (!isDragging) return;
          isDragging = false;
          track.classList.remove("is-dragging");
          if (track.hasPointerCapture(event.pointerId)) {
            track.releasePointerCapture(event.pointerId);
          }
          if (dragStarted) {
            suppressClickUntil = Date.now() + 220;
            track.dataset.suppressClickUntil = String(suppressClickUntil);
            setTimeout(() => {
              track.dataset.dragged = "false";
            }, 240);
          }
        }

        track.addEventListener("pointerup", endDrag);
        track.addEventListener("pointercancel", endDrag);
        track.addEventListener("lostpointercapture", () => {
          isDragging = false;
          track.classList.remove("is-dragging");
        });

        updateButtons();
        startAutoSlide();
      });

      const lightbox = document.getElementById("image-lightbox");
      const lightboxImage = lightbox.querySelector("img");
      const lightboxClose = lightbox.querySelector(".lightbox-close");
      const zoomableItems =
        ".result-case-media, .store-main-media, .work-card, .social-slide, .testimonial-card, .hero-card, .logo-item";

      function openLightboxFromImage(image) {
        lightboxImage.src = image.currentSrc || image.src;
        lightboxImage.alt = image.alt || "صورة من الأعمال";
        lightbox.classList.add("is-open");
        lightbox.setAttribute("aria-hidden", "false");
        document.body.style.overflow = "hidden";
      }

      document.querySelectorAll(`${zoomableItems} img`).forEach((image) => {
        image.setAttribute("draggable", "false");
        image.addEventListener("dragstart", (event) => event.preventDefault());
      });

      document.querySelectorAll(zoomableItems).forEach((item) => {
        item.addEventListener("click", (event) => {
          const track = event.currentTarget.closest(".gallery-track");
          const suppressUntil = Number(track?.dataset.suppressClickUntil || 0);
          if (track?.dataset.dragged === "true" || Date.now() < suppressUntil)
            return;

          const image = event.currentTarget.querySelector("img");
          if (image) openLightboxFromImage(image);
        });
      });

      function closeLightbox() {
        lightbox.classList.remove("is-open");
        lightbox.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
        setTimeout(() => {
          if (!lightbox.classList.contains("is-open")) {
            lightboxImage.src = "";
          }
        }, 250);
      }

      lightboxClose.addEventListener("click", closeLightbox);
      lightbox.addEventListener("click", (event) => {
        if (event.target === lightbox) closeLightbox();
      });
      document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && lightbox.classList.contains("is-open")) {
          closeLightbox();
        }
      });

      ScrollTrigger.create({
        trigger: "body",
        start: "top top",
        end: "bottom bottom",
        onUpdate: (self) => {
          progress.style.width = `${self.progress * 100}%`;
        },
      });
