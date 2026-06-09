document.addEventListener('DOMContentLoaded', () => {
    // Use injected programData from pendaftaran.php
    let programData = window.programData || {};
    
     // Log warning if programData is empty
    if (!programData || Object.keys(programData).length === 0) {
    console.warn('Program data is empty or not provided. Using fallback data.');
    programData = {
        sd: {
            name: "Program SD (Sekolah Dasar)",
            description: "Program pembelajaran lengkap untuk siswa SD kelas 1-6 meliputi semua mata pelajaran utama (kecuali agama dan olahraga) dengan metode yang menyenangkan dan interaktif.",
            subjects: ["Matematika", "Bahasa Indonesia", "IPA", "IPS", "Bahasa Inggris", "Seni Budaya", "PKn"],
            packages: {
                kelas_reguler: {
                    name: "Kelas Reguler",
                    description: "Max 5 Siswa : 1 Guru (Di tempat bimbel)",
                    prices: {
                        "8x": 160000,
                        "12x": 240000,
                        harian: 30000
                    },
                    additional: null
                },
                kelas_private_petung_girimukti: {
                    name: "Kelas Private - Petung/Girimukti",
                    description: "1 Siswa : 1 Guru (Guru datang ke rumah)",
                    prices: {
                        "8x": 200000,
                        "12x": 300000,
                        harian: 35000
                    },
                    additional: null
                },
                kelas_private_luar_petung_girimukti: {
                    name: "Kelas Private - Luar Petung/Girimukti",
                    description: "1 Siswa : 1 Guru (Guru datang ke rumah)",
                    prices: {
                        "8x": 240000,
                        "12x": 360000,
                        harian: 40000
                    },
                    additional: "*Tambahan biaya transportasi guru: Rp 6.250/pertemuan"
                }
            }
        },
        smp: {
            name: "Program SMP (Sekolah Menengah Pertama)",
            description: "Program yang dirancang khusus untuk siswa SMP dengan fokus pada tiga mata pelajaran inti: Matematika, IPA, dan Bahasa Inggris untuk membangun fondasi akademik yang kuat.",
            subjects: ["Matematika", "IPA (Fisika & Biologi)", "Bahasa Inggris"],
            packages: {
                kelas_reguler: {
                    name: "Kelas Reguler",
                    description: "Max 5 Siswa : 1 Guru (Di tempat bimbel)",
                    prices: {
                        "8x": 200000,
                        "12x": 300000,
                        harian: 35000
                    },
                    additional: null
                },
                kelas_private_petung_girimukti: {
                    name: "Kelas Private - Petung/Girimukti",
                    description: "1 Siswa : 1 Guru (Guru datang ke rumah)",
                    prices: {
                        "8x": 240000,
                        "12x": 360000,
                        harian: 40000
                    },
                    additional: null
                },
                kelas_private_luar_petung_girimukti: {
                    name: "Kelas Private - Luar Petung/Girimukti",
                    description: "1 Siswa : 1 Guru (Guru datang ke rumah)",
                    prices: {
                        "8x": 280000,
                        "12x": 420000,
                        harian: 45000
                    },
                    additional: "*Tambahan biaya transportasi guru: Rp 6.250/pertemuan"
                }
            }
        },
        sma: {
            name: "Program SMA (Sekolah Menengah Atas)",
            description: "Program intensif untuk siswa SMA dengan fokus pada mata pelajaran sains. Persiapan optimal untuk masuk perguruan tinggi jurusan sains dan teknik.",
            subjects: ["Matematika", "Fisika", "Kimia", "Biologi"],
            packages: {
                kelas_reguler: {
                    name: "Kelas Reguler",
                    description: "Max 5 Siswa : 1 Guru (Di tempat bimbel)",
                    prices: {
                        "8x": 240000,
                        "12x": 360000,
                        harian: 40000
                    },
                    additional: null
                },
                kelas_private_petung_girimukti: {
                    name: "Kelas Private - Petung/Girimukti",
                    description: "1 Siswa : 1 Guru (Guru datang ke rumah)",
                    prices: {
                        "8x": 320000,
                        "12x": 480000,
                        harian: 45000
                    },
                    additional: null
                },
                kelas_private_luar_petung_girimukti: {
                    name: "Kelas Private - Luar Petung/Girimukti",
                    description: "1 Siswa : 1 Guru (Guru datang ke rumah)",
                    prices: {
                        "8x": 360000,
                        "12x": 540000,
                        harian: 50000
                    },
                    additional: "*Tambahan biaya transportasi guru: Rp 6.250/pertemuan"
                }
            }
        }
    };
}
    initializeForm();

    // Fungsi inisialisasi form
    function initializeForm() {
        const mobileMenu = document.querySelector('.mobile-menu');
        const navMenu = document.querySelector('.nav-menu');
        
        // Toggle menu saat mobile menu diklik
        if (mobileMenu && navMenu) {
            mobileMenu.addEventListener('click', () => {
                navMenu.classList.toggle('active');
            });
        }

        // Hero Slider
        let currentSlideIndex = 0;
        const slides = document.querySelectorAll(".slide");
        const dots = document.querySelectorAll(".nav-dot");
        const linkStatus = document.getElementById("linkStatus");

        // Fungsi untuk menampilkan slide
        function showSlide(index) {
            slides.forEach((slide) => slide.classList.remove("active"));
            dots.forEach((dot) => dot.classList.remove("active"));
            slides[index].classList.add("active");
            dots[index].classList.add("active");
            const bgGradient = slides[index].getAttribute("data-bg");
            if (bgGradient) {
                slides[index].style.background = bgGradient;
            }
        }

        window.addEventListener("scroll", function () {
            const header = document.querySelector(".header");
            if (window.scrollY > 50) {
                header.classList.add("scrolled");
            } else {
                header.classList.remove("scrolled");
            }
        });

        // Fungsi navigasi
        function nextSlide() {
            currentSlideIndex = (currentSlideIndex + 1) % slides.length;
            showSlide(currentSlideIndex);
        }

        function previousSlide() {
            currentSlideIndex = (currentSlideIndex - 1 + slides.length) % slides.length;
            showSlide(currentSlideIndex);
        }

        function currentSlide(index) {
            currentSlideIndex = index - 1;
            showSlide(currentSlideIndex);
        }

        // Auto slide
        if (slides.length > 0) {
            setInterval(nextSlide, 5000);
        }

        // Simulasi navigasi untuk demo
        function simulateNavigation(targetPage) {
            if (linkStatus) {
                linkStatus.textContent = `Navigating to: ${targetPage}`;
                linkStatus.className = "link-status success";
                setTimeout(() => {
                    linkStatus.textContent = `✓ Successfully loaded: ${targetPage}`;
                }, 1000);
                setTimeout(() => {
                    linkStatus.textContent = "Link Status: Ready";
                    linkStatus.className = "link-status";
                }, 3000);
            }
        }

        // Fungsi untuk mengecek apakah file exists
        function checkFileExists(url) {
            return fetch(url, { method: "HEAD" })
                .then((response) => response.ok)
                .catch(() => false);
        }

        document.addEventListener("DOMContentLoaded", function () {
            const links = document.querySelectorAll("a[href]");
            links.forEach((link) => {
                link.addEventListener("click", function (e) {
                    const href = this.getAttribute("href");
                    if (href.startsWith("#")) {
                        return;
                    }
                    e.preventDefault();
                    simulateNavigation(href);
                });
            });
        });

        // Keyboard navigation
        document.addEventListener("keydown", function (e) {
            if (e.key === "ArrowLeft") {
                previousSlide();
            } else if (e.key === "ArrowRight") {
                nextSlide();
            }
        });

        // Testimonial Slider
        const testimonialSlides = document.querySelectorAll(".testimonial-slide");
        const testimonialIndicators = document.querySelectorAll(".testimonial-indicator");
        const testimonialPrev = document.querySelector(".testimonial-prev");
        const testimonialNext = document.querySelector(".testimonial-next");

        let currentTestimonial = 0;
        const totalTestimonials = testimonialSlides.length;

        function showTestimonial(index) {
            testimonialSlides.forEach((slide) => slide.classList.remove("active"));
            testimonialIndicators.forEach((indicator) =>
                indicator.classList.remove("active")
            );
            if (testimonialSlides[index]) {
                testimonialSlides[index].classList.add("active");
            }
            if (testimonialIndicators[index]) {
                testimonialIndicators[index].classList.add("active");
            }
        }

        function nextTestimonial() {
            currentTestimonial = (currentTestimonial + 1) % totalTestimonials;
            showTestimonial(currentTestimonial);
        }

        function prevTestimonial() {
            currentTestimonial =
                (currentTestimonial - 1 + totalTestimonials) % totalTestimonials;
            showTestimonial(currentTestimonial);
        }

        if (testimonialNext) {
            testimonialNext.addEventListener("click", nextTestimonial);
        }

        if (testimonialPrev) {
            testimonialPrev.addEventListener("click", prevTestimonial);
        }

        testimonialIndicators.forEach((indicator, index) => {
            indicator.addEventListener("click", () => {
                currentTestimonial = index;
                showTestimonial(currentTestimonial);
            });
        });

        if (testimonialSlides.length > 0) {
            setInterval(nextTestimonial, 7000);
        }

        // Gallery Image Click
        const galleryItems = document.querySelectorAll(".gallery-item img");
        galleryItems.forEach((img) => {
            img.addEventListener("click", function () {
                const overlay = document.createElement("div");
                overlay.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.8);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                    cursor: pointer;
                `;
                const modalImg = document.createElement("img");
                modalImg.src = this.src;
                modalImg.style.cssText = `
                    max-width: 90%;
                    max-height: 90%;
                    object-fit: contain;
                `;
                overlay.appendChild(modalImg);
                document.body.appendChild(overlay);
                overlay.addEventListener("click", () => {
                    document.body.removeChild(overlay);
                });
            });
        });

        // Smooth Scrolling
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach((link) => {
            link.addEventListener("click", function (e) {
                const href = this.getAttribute("href");
                if (href === "#") return;
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: "smooth",
                        block: "start"
                    });
                }
            });
        });

        // Header scroll effect
        const header = document.querySelector(".header");
        if (header) {
            window.addEventListener("scroll", () => {
                if (window.scrollY > 100) {
                    header.classList.add("scrolled");
                } else {
                    header.classList.remove("scrolled");
                }
            });
        }

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: "0px 0px -50px 0px"
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = "1";
                    entry.target.style.transform = "translateY(0)";
                }
            });
        }, observerOptions);

        const animateElements = document.querySelectorAll(
            ".program-card, .keunggulan-item, .testimonial-card, .gallery-item"
        );

        animateElements.forEach((el, index) => {
            el.style.opacity = "0";
            el.style.transform = "translateY(30px)";
            el.style.transition = `opacity 0.6s ease ${
                index * 0.1
            }s, transform 0.6s ease ${index * 0.1}s`;
            observer.observe(el);
        });

        // Back to top button
        const backToTop = document.createElement("button");
        backToTop.innerHTML = '<i class="fas fa-chevron-up"></i>';
        backToTop.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            z-index: 100;
            transition: all 0.3s ease;
        `;
        document.body.appendChild(backToTop);

        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) {
                backToTop.style.display = "block";
            } else {
                backToTop.style.display = "none";
            }
        });

        backToTop.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });

        // Hover effects
        const interactiveElements = document.querySelectorAll(
            ".btn, .program-card, .keunggulan-item, .gallery-item"
        );
        interactiveElements.forEach((el) => {
            el.addEventListener("mouseenter", function () {
                this.style.transform = "translateY(-2px)";
            });
            el.addEventListener("mouseleave", function () {
                this.style.transform = "translateY(0)";
            });
        });

        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0
            }).format(amount);
        }

        // FAQ Toggle Functionality
        const faqQuestions = document.querySelectorAll(".faq-question");
        faqQuestions.forEach((question) => {
            question.addEventListener("click", function () {
                const faqItem = this.parentElement;
                const faqAnswer = faqItem.querySelector(".faq-answer");
                faqQuestions.forEach((otherQuestion) => {
                    if (otherQuestion !== this) {
                        otherQuestion.classList.remove("active");
                        otherQuestion.parentElement
                            .querySelector(".faq-answer")
                            .classList.remove("active");
                    }
                });
                this.classList.toggle("active");
                faqAnswer.classList.toggle("active");
            });
        });

        // Dynamic Class Selection
        const jenjangSelect = document.getElementById("jenjang");
        const kelasSelect = document.getElementById("kelas");
        const packageTypeSelect = document.getElementById("package_type");
        const durasiSelect = document.getElementById("durasi");
        const durasiGroup = document.getElementById("durasiGroup");
        const programDetails = document.getElementById("programDetails");
        const priceDisplay = document.getElementById("priceDisplay");
        const programSelection = document.getElementById("programSelection");

        let subjectSelectionContainer = null;

        // Class options for each jenjang
        const kelasOptions = {
            sd: ["1", "2", "3", "4", "5", "6"],
            smp: ["7", "8", "9"],
            sma: ["10", "11", "12"]
        };

        // Duration options
        const durasiOptions = [
            { value: "8x", label: "8x Pertemuan (1 Bulan)" },
            { value: "12x", label: "12x Pertemuan (1.5 Bulan)" },
            { value: "harian", label: "Harian (Per Pertemuan)" }
        ];

        // Tambahkan input jumlah hari untuk durasi harian
        let jumlahHariContainer = null;

        function createJumlahHariInput() {
            if (jumlahHariContainer) {
                jumlahHariContainer.remove();
            }

            jumlahHariContainer = document.createElement("div");
            jumlahHariContainer.className = "form-group";
            jumlahHariContainer.id = "jumlahHariGroup";
            jumlahHariContainer.innerHTML = `
                <label for="jumlahHari" style="display: block; margin-bottom: 10px; font-weight: 600; color: #333;">
                    Jumlah Hari <span class="required">*</span>
                </label>
                <input 
                    type="number" 
                    id="jumlahHari" 
                    name="jumlahHari" 
                    min="1" 
                    max="30" 
                    value="1"
                    style="
                        width: 100%;
                        padding: 12px;
                        border: 2px solid #e0e0e0;
                        border-radius: 8px;
                        font-size: 14px;
                        transition: border-color 0.3s ease;
                    "
                    required
                >
                <div id="jumlahHariError" style="
                    color: #e74c3c;
                    font-size: 12px;
                    margin-top: 5px;
                    display: none;
                "></div>
            `;

            durasiGroup.parentElement.appendChild(jumlahHariContainer);

            const jumlahHariInput = document.getElementById("jumlahHari");
            jumlahHariInput.addEventListener("input", () => {
                const errorDiv = document.getElementById("jumlahHariError");
                if (jumlahHariInput.value < 1 || jumlahHariInput.value > 30) {
                    errorDiv.textContent = "Jumlah hari harus antara 1-30 hari";
                    errorDiv.style.display = "block";
                    jumlahHariInput.style.borderColor = "#e74c3c";
                } else {
                    errorDiv.style.display = "none";
                    jumlahHariInput.style.borderColor = "";
                    updatePriceDisplay();
                }
            });
        }

        function createSubjectSelection(subjects) {
            if (subjectSelectionContainer) {
                subjectSelectionContainer.remove();
            }

            if (!subjects || subjects.length === 0) {
                subjectSelectionContainer = document.createElement("div");
                subjectSelectionContainer.className = "form-group";
                subjectSelectionContainer.id = "subjectSelection";
                subjectSelectionContainer.innerHTML = `
                    <div style="color: #e74c3c; font-size: 14px;">
                        Tidak ada mata pelajaran tersedia untuk jenjang ini.
                    </div>
                `;
                packageTypeSelect.parentElement.parentElement.insertBefore(
                    subjectSelectionContainer,
                    durasiGroup
                );
                return;
            }

            subjectSelectionContainer = document.createElement("div");
            subjectSelectionContainer.className = "form-group";
            subjectSelectionContainer.id = "subjectSelection";
            subjectSelectionContainer.innerHTML = `
                <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #333;">
                    Pilih Mata Pelajaran <span class="required">*</span>
                </label>
                <div class="subject-options" style="
                    display: grid; 
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
                    gap: 10px; 
                    margin-bottom: 15px;
                ">
                    ${subjects
                        .map(
                            (subject, index) => `
                        <label class="subject-checkbox" style="
                            display: flex; 
                            align-items: center; 
                            padding: 10px; 
                            border: 2px solid #e0e0e0; 
                            border-radius: 8px; 
                            cursor: pointer; 
                            transition: all 0.3s ease;
                            background: #f8f9fa;
                        ">
                            <input 
                                type="checkbox" 
                                name="mataPelajaran[]" 
                                value="${subject}" 
                                id="subject_${index}"
                                style="margin-right: 8px; transform: scale(1.2);"
                            >
                            <span style="font-size: 14px; font-weight: 500;">${subject}</span>
                        </label>
                    `
                        )
                        .join("")}
                </div>
                <div class="select-all-container" style="margin-bottom: 15px;">
                    <button type="button" id="selectAllSubjects" style="
                        background: #667eea; 
                        color: white; 
                        border: none; 
                        padding: 8px 15px; 
                        border-radius: 5px; 
                        cursor: pointer; 
                        font-size: 12px;
                        margin-right: 10px;
                    ">
                        Pilih Semua
                    </button>
                    <button type="button" id="clearAllSubjects" style="
                        background: #e74c3c; 
                        color: white; 
                        border: none; 
                        padding: 8px 15px; 
                        border-radius: 5px; 
                        cursor: pointer; 
                        font-size: 12px;
                    ">
                        Hapus Semua
                    </button>
                </div>
                <div id="selectedSubjectsCount" style="
                    font-size: 14px; 
                    color: #667eea; 
                    font-weight: 600; 
                    margin-top: 10px;
                "></div>
            `;

            const packageTypeGroup = packageTypeSelect.parentElement;
            packageTypeGroup.parentElement.insertBefore(
                subjectSelectionContainer,
                durasiGroup
            );

            const subjectCheckboxes = subjectSelectionContainer.querySelectorAll(
                'input[name="mataPelajaran[]"]'
            );
            const selectAllBtn = document.getElementById("selectAllSubjects");
            const clearAllBtn = document.getElementById("clearAllSubjects");
            const selectedCountDiv = document.getElementById("selectedSubjectsCount");

            subjectCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener("change", function () {
                    const label = this.closest(".subject-checkbox");
                    if (this.checked) {
                        label.style.borderColor = "#667eea";
                        label.style.backgroundColor = "#e8f0fe";
                        label.style.transform = "scale(1.02)";
                    } else {
                        label.style.borderColor = "#e0e0e0";
                        label.style.backgroundColor = "#f8f9fa";
                        label.style.transform = "scale(1)";
                    }
                    console.log("Selected Subjects:", getSelectedSubjects());
                    updateSelectedCount();
                    updatePriceDisplay();
                });
            });

            selectAllBtn.addEventListener("click", function () {
                subjectCheckboxes.forEach((checkbox) => {
                    checkbox.checked = true;
                    checkbox.dispatchEvent(new Event("change"));
                });
            });

            clearAllBtn.addEventListener("click", function () {
                subjectCheckboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                    checkbox.dispatchEvent(new Event("change"));
                });
            });

            function updateSelectedCount() {
                const selectedCount = Array.from(subjectCheckboxes).filter(
                    (cb) => cb.checked
                ).length;
                selectedCountDiv.textContent = `Dipilih: ${selectedCount} mata pelajaran`;

                if (selectedCount === 0) {
                    selectedCountDiv.style.color = "#e74c3c";
                    selectedCountDiv.textContent += " (minimal pilih 1 mata pelajaran)";
                } else {
                    selectedCountDiv.style.color = "#667eea";
                }
            }

            updateSelectedCount();
        }

        function getSelectedSubjects() {
            if (!subjectSelectionContainer) return [];
            const checkboxes = subjectSelectionContainer.querySelectorAll(
                'input[name="mataPelajaran[]"]:checked'
            );
            return Array.from(checkboxes).map((cb) => cb.value);
        }

        function updatePriceDisplay() {
            const selectedJenjang = jenjangSelect.value;
            const selectedPackage = packageTypeSelect.value;
            const selectedDurasi = durasiSelect.value;
            const selectedSubjects = getSelectedSubjects();
            const jumlahHariInput = document.getElementById("jumlahHari");
            const jumlahHari = jumlahHariInput ? parseInt(jumlahHariInput.value) || 1 : 1;

            if (
                selectedJenjang &&
                selectedPackage &&
                selectedDurasi &&
                selectedSubjects.length > 0 &&
                (selectedDurasi !== "harian" || (jumlahHari >= 1 && jumlahHari <= 30))
            ) {
                const program = programData[selectedJenjang];
                const packageData = program.packages[selectedPackage];
                const basePricePerSubject = packageData.prices[selectedDurasi];
                const subjectCount = selectedSubjects.length;

                let totalBasePrice;
                let transportCost = 0;

                if (selectedDurasi === "harian") {
                    totalBasePrice = basePricePerSubject * jumlahHari;
                    if (selectedPackage === "kelas_private_luar_petung_girimukti") {
                        transportCost = 6250 * jumlahHari;
                    }
                } else {
                    totalBasePrice = basePricePerSubject;
                    if (selectedPackage === "kelas_private_luar_petung_girimukti") {
                        const sessions = selectedDurasi === "8x" ? 8 : 12;
                        transportCost = 6250 * sessions;
                    }
                }

                // Calculate total price per subject and then multiply by number of subjects
                const totalPricePerSubject = totalBasePrice + transportCost;
                const totalPrice = totalPricePerSubject * subjectCount;

                let priceHTML = `
                    <h4>Biaya Program</h4>
                    <div class="price-breakdown">
                        <div style="margin-bottom: 15px; padding: 10px; background: rgba(255,255,255,0.1); border-radius: 5px;">
                            <strong>Mata Pelajaran Dipilih (${subjectCount}):</strong><br>
                            <ul style="margin: 5px 0; padding-left: 20px;">
                                ${selectedSubjects
                                    .map((subject) => `<li>${subject}</li>`)
                                    .join("")}
                            </ul>
                        <div style="font-size: 16px; margin-bottom: 10px;">
                            <strong>Biaya Pembelajaran:</strong> ${formatCurrency(
                                totalBasePrice * subjectCount
                            )}
                            <span style="font-size: 12px; opacity: 0.8;">(${subjectCount} mata pelajaran)</span>
                            <span style="font-size: 12px; opacity: 0.8;">(Pertemuan kelas selama ${
                                selectedDurasi === "harian" ? `${jumlahHari} hari` : selectedDurasi
                            } untuk tiap mata pelajaran)</span>
                        </div>
                `;

                if (selectedPackage === "kelas_private_luar_petung_girimukti" && transportCost > 0) {
                    priceHTML += `
                        <div style="font-size: 16px; margin-bottom: 10px;">
                            <strong>Biaya Transportasi untuk ${subjectCount} Mata Pelajaran:</strong> ${formatCurrency(
                                transportCost * subjectCount
                            )}
                        </div>
                        <hr style="border: 1px solid rgba(255,255,255,0.3); margin: 10px 0;">
                    `;
                }

                priceHTML += `
                        <div style="font-size: 28px; font-weight: bold; color: #ffd700; text-align: center; padding: 15px; background: rgba(255,215,0,0.1); border-radius: 10px; margin-top: 10px;">
                            <strong>TOTAL: ${formatCurrency(totalPrice)}</strong>
                        </div>
                    </div>
                `;

                if (selectedDurasi === "harian") {
                    priceHTML += `<div style="font-size: 14px; margin-top: 5px; text-align: center;">Untuk ${jumlahHari} Hari</div>`;
                } else {
                    const sessions = selectedDurasi === "8x" ? 8 : 12;
                    const pricePerSession = Math.round(totalPricePerSubject / sessions);
                    priceHTML += `
                        <div style="font-size: 14px; margin-top: 5px; text-align: center;">
                            ${sessions} Pertemuan (${formatCurrency(pricePerSession)}/pertemuan per mata pelajaran)
                        </div>
                    `;
                }

                priceHTML += `
                    <div style="font-size: 12px; margin-top: 15px; padding: 10px; background: rgba(52, 152, 219, 0.1); border-radius: 5px; border-left: 3px solid #3498db;">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Rincian Biaya:</strong><br>
                        • Biaya pembelajaran per mata pelajaran = ${formatCurrency(
                            totalBasePrice
                        )}<br>
                        ${
                            selectedPackage === "kelas_private_luar_petung_girimukti"
                                ? `• Biaya transportasi per mata pelajaran = ${formatCurrency(
                                    transportCost
                                )}<br>`
                                : ""
                        }
                        • Total per mata pelajaran = ${formatCurrency(totalPricePerSubject)}<br>
                        • Total untuk ${subjectCount} mata pelajaran = ${formatCurrency(totalPrice)}
                    </div>
                `;

                document.getElementById("priceInfo").innerHTML = priceHTML;
                priceDisplay.classList.add("show");
            } else {
                priceDisplay.classList.remove("show");
            }
        }

        jenjangSelect.addEventListener("change", function () {
            const selectedJenjang = this.value;
            // Reset dependent fields
            kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
            packageTypeSelect.innerHTML = '<option value="">Pilih Paket Program</option>';
            durasiSelect.innerHTML = '<option value="">Pilih Durasi</option>';
            durasiGroup.style.display = "none";
            if (jumlahHariContainer) {
                jumlahHariContainer.remove();
                jumlahHariContainer = null;
            }
            programDetails.classList.remove("show");
            priceDisplay.classList.remove("show");
            programSelection.classList.remove("active");

            if (subjectSelectionContainer) {
                subjectSelectionContainer.remove();
                subjectSelectionContainer = null;
            }

            if (selectedJenjang) {
                // Populate kelas options
                if (kelasOptions[selectedJenjang]) {
                    kelasOptions[selectedJenjang].forEach((kelas) => {
                        const option = document.createElement("option");
                        option.value = kelas;
                        option.textContent = `Kelas ${kelas}`;
                        kelasSelect.appendChild(option);
                    });
                } else {
                    console.warn(`No class options found for jenjang: ${selectedJenjang}`);
                    kelasSelect.innerHTML = '<option value="">Kelas tidak tersedia</option>';
                }

                // Populate package options
                const program = programData[selectedJenjang];
                if (program && program.packages) {
                    Object.keys(program.packages).forEach((packageKey) => {
                        const packageData = program.packages[packageKey];
                        const option = document.createElement("option");
                        option.value = packageKey;
                        option.textContent = packageData.name;
                        packageTypeSelect.appendChild(option);
                    });
                } else {
                    console.warn(`No program data found for jenjang: ${selectedJenjang}`);
                    packageTypeSelect.innerHTML = '<option value="">Paket tidak tersedia</option>';
                }
            }
        });

        packageTypeSelect.addEventListener("change", function () {
            const selectedJenjang = jenjangSelect.value;
            const selectedPackage = this.value;

            durasiSelect.innerHTML = '<option value="">Pilih Durasi</option>';
            programDetails.classList.remove("show");
            priceDisplay.classList.remove("show");

            if (jumlahHariContainer) {
                jumlahHariContainer.remove();
                jumlahHariContainer = null;
            }

            if (subjectSelectionContainer) {
                subjectSelectionContainer.remove();
                subjectSelectionContainer = null;
            }

            if (selectedJenjang && selectedPackage) {
                programSelection.classList.add("active");

                const program = programData[selectedJenjang];
                const packageData = program.packages[selectedPackage];

                let programInfoHTML = `
                    <h4>${packageData.name}</h4>
                    <p><strong>Deskripsi:</strong> ${packageData.description}</p>
                    <div class="program-features">
                        <strong>Mata Pelajaran Tersedia:</strong>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;">
                            ${program.subjects
                                .map(
                                    (subject) => `
                                    <span style="
                                        background: #667eea; 
                                        color: white; 
                                        padding: 4px 8px; 
                                        border-radius: 15px; 
                                        font-size: 12px;
                                        font-weight: 500;
                                    ">${subject}</span>
                                `
                                )
                                .join("")}
                        </div>
                    </div>
                `;

                if (packageData.additional) {
                    programInfoHTML += `
                        <div class="additional-note" style="
                            background: #fff3cd; 
                            border: 1px solid #ffeaa7; 
                            padding: 10px; 
                            border-radius: 5px; 
                            margin-top: 10px; 
                            font-size: 14px;
                        ">
                            <strong>Catatan:</strong> ${packageData.additional}
                        </div>
                    `;
                }

                document.getElementById("programInfo").innerHTML = programInfoHTML;
                programDetails.classList.add("show");

                createSubjectSelection(program.subjects);

                durasiOptions.forEach((durasi) => {
                    if (packageData.prices[durasi.value]) {
                        const option = document.createElement("option");
                        option.value = durasi.value;
                        option.textContent = durasi.label;
                        durasiSelect.appendChild(option);
                    }
                });

                durasiGroup.style.display = "block";
            } else {
                programSelection.classList.remove("active");
                durasiGroup.style.display = "none";
            }
        });

        durasiSelect.addEventListener("change", function () {
            const selectedDurasi = this.value;
            if (selectedDurasi === "harian") {
                createJumlahHariInput();
            } else if (jumlahHariContainer) {
                jumlahHariContainer.remove();
                jumlahHariContainer = null;
            }
            updatePriceDisplay();
        });

        // Form Validation and Submission
        const registrationForm = document.getElementById("registrationForm");
        registrationForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const requiredFields = [
                "namaLengkap",
                "tanggalLahir",
                "jenisKelamin",
                "alamat",
                "telepon",
                "jenjang",
                "kelas",
                "sekolah",
                "package_type",
                "durasi",
                "namaOrtu",
                "teleponOrtu",
                "persetujuan"
            ];

            let isValid = true;
            let missingFields = [];

            requiredFields.forEach((fieldName) => {
                const field = document.getElementById(fieldName);
                if (!field || !field.value.trim()) {
                    isValid = false;
                    missingFields.push(fieldName);
                    if (field) {
                        field.style.borderColor = "#e74c3c";
                    }
                } else {
                    if (field) {
                        field.style.borderColor = "";
                    }
                }
            });

            const selectedSubjects = getSelectedSubjects();
            document.getElementById('mataPelajaranHidden').value = selectedSubjects.join(',');
            if (selectedSubjects.length === 0) {
                isValid = false;
                alert("Mohon pilih minimal satu mata pelajaran!");
                if (subjectSelectionContainer) {
                    subjectSelectionContainer.style.border = "2px solid #e74c3c";
                    subjectSelectionContainer.scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });
                }
                return false;
            }

            const jumlahHariInput = document.getElementById("jumlahHari");
            if (durasiSelect.value === "harian" && jumlahHariInput) {
                const jumlahHari = parseInt(jumlahHariInput.value);
                if (jumlahHari < 1 || jumlahHari > 30) {
                    isValid = false;
                    alert("Jumlah hari harus antara 1-30 hari");
                    jumlahHariInput.focus();
                    return false;
                }
            }

            let subjectsInput = document.getElementById("mataPelajaranHidden");
            if (!subjectsInput) {
                subjectsInput = document.createElement("input");
                subjectsInput.type = "hidden";
                subjectsInput.id = "mataPelajaranHidden";
                subjectsInput.name = "mataPelajaran";
                registrationForm.appendChild(subjectsInput);
            }
            subjectsInput.value = selectedSubjects.join(",");

            if (jumlahHariInput) {
                let jumlahHariInputHidden = document.getElementById("jumlahHariHidden");
                if (!jumlahHariInputHidden) {
                    jumlahHariInputHidden = document.createElement("input");
                    jumlahHariInputHidden.type = "hidden";
                    jumlahHariInputHidden.id = "jumlahHariHidden";
                    jumlahHariInputHidden.name = "jumlahHari";
                    registrationForm.appendChild(jumlahHariInputHidden);
                }
                jumlahHariInputHidden.value = jumlahHariInput.value;
            }

            const persetujuanCheckbox = document.getElementById("persetujuan");
            if (!persetujuanCheckbox.checked) {
                isValid = false;
                missingFields.push("persetujuan");
            }

            if (!isValid) {
                alert("Mohon lengkapi semua field yang wajib diisi (bertanda *)");
                return false;
            }

            const teleponRegex = /^[0-9+\-\s()]+$/;
            const telepon = document.getElementById("telepon").value;
            const teleponOrtu = document.getElementById("teleponOrtu").value;

            if (!teleponRegex.test(telepon)) {
                alert("Format nomor telepon siswa tidak valid");
                document.getElementById("telepon").focus();
                return false;
            }

            if (!teleponRegex.test(teleponOrtu)) {
                alert("Format nomor telepon orang tua tidak valid");
                document.getElementById("teleponOrtu").focus();
                return false;
            }

            const email = document.getElementById("email").value;
            if (email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert("Format email tidak valid");
                    document.getElementById("email").focus();
                    return false;
                }
            }

            const tanggalLahir = new Date(
                document.getElementById("tanggalLahir").value
            );
            const today = new Date();
            const age = today.getFullYear() - tanggalLahir.getFullYear();

            if (age < 6 || age > 20) {
                alert("Usia siswa harus antara 6-20 tahun");
                document.getElementById("tanggalLahir").focus();
                return false;
            }

            showLoadingState();
            registrationForm.submit();
        });

        function showSuccessMessage(selectedSubjects) {
            const modal = document.createElement("div");
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 10000;
            `;
            const modalContent = document.createElement("div");
            modalContent.style.cssText = `
                background: white;
                padding: 30px;
                border-radius: 15px;
                text-align: center;
                max-width: 500px;
                margin: 20px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            `;
            modalContent.innerHTML = `
                <div style="color: #28a745; font-size: 48px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 style="color: #667eea; margin-bottom: 15px;">Pendaftaran Berhasil!</h3>
                <p style="margin-bottom: 15px; line-height: 1.6;">
                    Terima kasih telah mendaftar di Bimbingan Belajar Peta Ilmu.<br>
                    <strong>Mata Pelajaran yang dipilih:</strong>
                </p>
                <div style="margin-bottom: 20px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                    <div style="display: flex; flex-wrap: wrap; gap: 5px; justify-content: center;">
                        ${selectedSubjects
                            .map(
                                (subject) => `
                                <span style="
                                    background: #667eea; 
                                    color: white; 
                                    padding: 4px 8px; 
                                    border-radius: 12px; 
                                    font-size: 12px;
                                    font-weight: 500;
                                ">${subject}</span>
                            `
                            )
                            .join("")}
                    </div>
                </div>
                <p style="margin-bottom: 25px; line-height: 1.6;">
                    Tim kami akan menghubungi Anda dalam 1x24 jam untuk konfirmasi lebih lanjut.
                </p>
                <button onclick="location.reload()" style="
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border: none;
                    padding: 12px 25px;
                    border-radius: 25px;
                    cursor: pointer;
                    font-weight: 600;
                    transition: transform 0.2s ease;
                " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    Tutup
                </button>
            `;
            modal.appendChild(modalContent);
            document.body.appendChild(modal);
            registrationForm.reset();
            kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
            packageTypeSelect.innerHTML =
                '<option value="">Pilih jenjang pendidikan terlebih dahulu</option>';
            durasiSelect.innerHTML = '<option value="">Pilih Durasi</option>';
            durasiGroup.style.display = "none";
            if (jumlahHariContainer) {
                jumlahHariContainer.remove();
                jumlahHariContainer = null;
            }
            programDetails.classList.remove("show");
            priceDisplay.classList.remove("show");
            programSelection.classList.remove("active");
            if (subjectSelectionContainer) {
                subjectSelectionContainer.remove();
                subjectSelectionContainer = null;
            }
        }

        // Phone number formatting
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach((input) => {
            input.addEventListener("input", function (e) {
                let value = e.target.value.replace(/[^\d+\-]/g, "");
                if (value.startsWith("08")) {
                    value = value.replace(/(\d{4})(\d{4})(\d+)/, "$1-$2-$3");
                } else if (value.startsWith("+62")) {
                    value = value.replace(/(\+62)(\d{3})(\d{4})(\d+)/, "$1-$2-$3-$4");
                }
                e.target.value = value;
            });
        });

        // Smooth scrolling
        const navLinks = document.querySelectorAll('a[href^="#"]');
        navLinks.forEach((link) => {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                const targetId = this.getAttribute("href");
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: "smooth",
                        block: "start"
                    });
                }
            });
        });

        // Form field styling
        const formInputs = document.querySelectorAll("input, select, textarea");
        formInputs.forEach((input) => {
            input.addEventListener("focus", function () {
                this.style.borderColor = "#667eea";
                this.style.boxShadow = "0 0 5px rgba(102, 126, 234, 0.3)";
            });
            input.addEventListener("blur", function () {
                this.style.borderColor = "";
                this.style.boxShadow = "";
            });
        });

        // Loading animation
        function showLoadingState() {
            const submitButton = document.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            }
        }

        function resetButtonState() {
            const submitButton = document.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Pendaftaran';
            }
        }

        function animatePriceDisplay() {
            const priceInfo = document.getElementById("priceInfo");
            if (priceInfo) {
                priceInfo.style.opacity = "0";
                priceInfo.style.transform = "translateY(20px)";
                setTimeout(() => {
                    priceInfo.style.transition = "all 0.5s ease";
                    priceInfo.style.opacity = "1";
                    priceInfo.style.transform = "translateY(0)";
                }, 100);
            }
        }

        const originalUpdatePriceDisplay = updatePriceDisplay;
        updatePriceDisplay = function () {
            originalUpdatePriceDisplay();
            if (priceDisplay.classList.contains("show")) {
                animatePriceDisplay();
            }
        };

        // Tooltip functionality
        const helpIcons = document.querySelectorAll(".help-icon");
        helpIcons.forEach((icon) => {
            icon.addEventListener("mouseenter", function () {
                const tooltip = this.querySelector(".tooltip");
                if (tooltip) {
                    tooltip.style.display = "block";
                    tooltip.style.opacity = "1";
                }
            });
            icon.addEventListener("mouseleave", function () {
                const tooltip = this.querySelector(".tooltip");
                if (tooltip) {
                    tooltip.style.opacity = "0";
                    setTimeout(() => {
                        tooltip.style.display = "none";
                    }, 200);
                }
            });
        });

        // Progressive validation
        function validateFieldOnBlur(field) {
            const value = field.value.trim();
            const fieldName = field.name || field.id;
            field.style.borderColor = "";
            const existingError = field.parentElement.querySelector(".error-message");
            if (existingError) {
                existingError.remove();
            }
            let errorMessage = "";
            switch (fieldName) {
                case "namaLengkap":
                    if (!value) {
                        errorMessage = "Nama lengkap wajib diisi";
                    } else if (value.length < 2) {
                        errorMessage = "Nama minimal 2 karakter";
                    }
                    break;
                case "telepon":
                case "teleponOrtu":
                    if (!value) {
                        errorMessage = "Nomor telepon wajib diisi";
                    } else if (!/^[0-9+\-\s()]+$/.test(value)) {
                        errorMessage = "Format nomor telepon tidak valid";
                    }
                    break;
                case "email":
                    if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                        errorMessage = "Format email tidak valid";
                    }
                    break;
                case "tanggalLahir":
                    if (!value) {
                        errorMessage = "Tanggal lahir wajib diisi";
                    } else {
                        const birthDate = new Date(value);
                        const today = new Date();
                        const age = today.getFullYear() - birthDate.getFullYear();
                        if (age < 6 || age > 20) {
                            errorMessage = "Usia harus antara 6-20 tahun";
                        }
                    }
                    break;
            }
            if (errorMessage) {
                field.style.borderColor = "#e74c3c";
                const errorDiv = document.createElement("div");
                errorDiv.className = "error-message";
                errorDiv.style.cssText = `
                    color: #e74c3c;
                    font-size: 12px;
                    margin-top: 5px;
                    font-weight: 500;
                `;
                errorDiv.textContent = errorMessage;
                field.parentElement.appendChild(errorDiv);
                return false;
            }
            return true;
        }

        const validatableFields = document.querySelectorAll(
            "input[required], select[required], textarea[required]"
        );
        validatableFields.forEach((field) => {
            field.addEventListener("blur", function () {
                validateFieldOnBlur(this);
            });
        });

        // Auto-capitalize names
        const nameFields = document.querySelectorAll(
            'input[name="namaLengkap"], input[name="namaOrtu"]'
        );
        nameFields.forEach((field) => {
            field.addEventListener("input", function () {
                const words = this.value.split(" ");
                const capitalizedWords = words.map((word) => {
                    if (word.length > 0) {
                        return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
                    }
                    return word;
                });
                this.value = capitalizedWords.join(" ");
            });
        });

        // Prevent Enter key submission
        registrationForm.addEventListener("keydown", function (e) {
            if (
                e.key === "Enter" &&
                e.target.tagName !== "TEXTAREA" &&
                e.target.type !== "submit"
            ) {
                e.preventDefault();
                const formElements = Array.from(
                    this.querySelectorAll("input, select, textarea, button")
                );
                const currentIndex = formElements.indexOf(e.target);
                const nextElement = formElements[currentIndex + 1];
                if (nextElement) {
                    nextElement.focus();
                }
            }
        });

        // Character counter for textarea
        const textareas = document.querySelectorAll("textarea");
        textareas.forEach((textarea) => {
            const maxLength = textarea.getAttribute("maxlength") || 500;
            const counter = document.createElement("div");
            counter.style.cssText = `
                font-size: 12px;
                color: #666;
                text-align: right;
                margin-top: 5px;
            `;
            textarea.parentElement.appendChild(counter);
            function updateCounter() {
                const remaining = maxLength - textarea.value.length;
                counter.textContent = `${textarea.value.length}/${maxLength} karakter`;
                counter.style.color = remaining < 50 ? "#e74c3c" : "#666";
            }
            textarea.addEventListener("input", updateCounter);
            updateCounter();
        });

        // Accessibility
        const formGroups = document.querySelectorAll(".form-group");
        formGroups.forEach((group) => {
            const label = group.querySelector("label");
            const input = group.querySelector("input, select, textarea");
            if (label && input) {
                const labelText = label.textContent.replace("*", "").trim();
                input.setAttribute("aria-label", labelText);
                if (input.hasAttribute("required")) {
                    input.setAttribute("aria-required", "true");
                }
            }
        });

        // Keyboard navigation
        document.addEventListener("keydown", function (e) {
            if (e.key === "Tab") {
                const focusableElements = document.querySelectorAll(
                    'input, select, textarea, button, [tabindex]:not([tabindex="-1"])'
                );
                focusableElements.forEach((el) => {
                    el.addEventListener("focus", function () {
                        this.style.outline = "2px solid #667eea";
                        this.style.outlineOffset = "2px";
                    });
                    el.addEventListener("blur", function () {
                        this.style.outline = "";
                        this.style.outlineOffset = "";
                    });
                });
            }
        });

        // Debounce price updates
        let priceUpdateTimeout;
        const debouncedPriceUpdate = function () {
            clearTimeout(priceUpdateTimeout);
            priceUpdateTimeout = setTimeout(updatePriceDisplay, 150);
        };

        if (subjectSelectionContainer) {
            const checkboxes = subjectSelectionContainer.querySelectorAll(
                'input[type="checkbox"]'
            );
            checkboxes.forEach((checkbox) => {
                checkbox.removeEventListener("change", updatePriceDisplay);
                checkbox.addEventListener("change", debouncedPriceUpdate);
            });
        }

        // Page load animation
        const loader = document.querySelector(".page-loader");
        if (loader) {
            loader.style.opacity = "0";
            setTimeout(() => {
                loader.style.display = "none";
            }, 300);
        }

        const mainContent = document.querySelector("main");
        if (mainContent) {
            mainContent.style.opacity = "0";
            mainContent.style.transform = "translateY(20px)";
            setTimeout(() => {
                mainContent.style.transition = "all 0.6s ease";
                mainContent.style.opacity = "1";
                mainContent.style.transform = "translateY(0)";
            }, 100);
        }

        // Lazy load images
        function lazyLoadImages() {
            const images = document.querySelectorAll("img[data-src]");
            if ("IntersectionObserver" in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove("lazy");
                            imageObserver.unobserve(img);
                        }
                    });
                });
                images.forEach((img) => imageObserver.observe(img));
            } else {
                images.forEach((img) => {
                    img.src = img.dataset.src;
                });
            }
        }
        lazyLoadImages();
    }

    // Utility functions
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});
