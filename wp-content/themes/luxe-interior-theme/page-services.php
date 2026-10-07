<?php
/**
 * Template Name: Services Page
 */
get_header(); ?>

<!-- PAGE HERO -->
<div class="page-hero" style="background:linear-gradient(135deg,#1A1410 0%,#2C2018 100%);">
    <div class="container">
        <span class="eyebrow" style="display:block;margin-bottom:1rem;"><?php esc_html_e('What We Offer', 'luxe-interior'); ?></span>
        <h1><?php esc_html_e('Our Services', 'luxe-interior'); ?></h1>
    </div>
</div>

<!-- SERVICES INTRO -->
<section>
    <div class="container">
        <div style="max-width:700px;margin-inline:auto;text-align:center;" data-reveal>
            <span class="eyebrow" style="display:block;margin-bottom:1rem;"><?php esc_html_e('How We Work', 'luxe-interior'); ?></span>
            <h2><?php esc_html_e('A Full-Spectrum Design Practice', 'luxe-interior'); ?></h2>
            <p style="margin-top:1.25rem;font-size:1.1rem;"><?php esc_html_e('From the first concept sketch to the final styling edit, we offer a comprehensive range of design and project management services. Every engagement is tailored to what you need.', 'luxe-interior'); ?></p>
        </div>
    </div>
</section>

<!-- SERVICES DETAILED -->
<section style="background:var(--color-cream);padding-top:0;">
    <div class="container">

        <!-- Service 1 -->
        <div class="about-section" style="grid-template-columns:1fr 1fr;display:grid;min-height:520px;margin-bottom:4px;">
            <div style="background:linear-gradient(135deg,#2C2420,#1A1614);display:flex;align-items:center;justify-content:center;" data-reveal="left">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="10" y="10" width="60" height="60" stroke="#B08D6A" stroke-width="1.5" fill="none"/>
                    <rect x="20" y="20" width="40" height="40" stroke="rgba(176,141,106,0.4)" stroke-width="1" fill="none"/>
                    <line x1="10" y1="40" x2="70" y2="40" stroke="#B08D6A" stroke-width="1.5"/>
                    <line x1="40" y1="10" x2="40" y2="70" stroke="#B08D6A" stroke-width="1.5"/>
                </svg>
            </div>
            <div class="about-content" data-reveal="right">
                <span class="eyebrow" style="display:block;margin-bottom:0.75rem;">01</span>
                <h2 style="font-size:2.2rem;"><?php esc_html_e('Residential Interior Design', 'luxe-interior'); ?></h2>
                <p style="margin-top:1.25rem;"><?php esc_html_e('Our flagship service — a complete, end-to-end interior design process for homes of every scale. From one-bedroom apartments to multi-floor villas, we bring the same rigor and care to each.', 'luxe-interior'); ?></p>
                <p><?php esc_html_e('We begin with an extended discovery phase: understanding how you live, what you value, and what spaces have moved you in the past. From there, we develop a concept, present material and furniture options, manage procurement and installation, and finally style your home to completion.', 'luxe-interior'); ?></p>
                <div style="margin-top:1.5rem;">
                    <?php $items = array( __('Full concept design & presentations','luxe-interior'), __('Floor plan & space planning','luxe-interior'), __('Material, finish & furniture specification','luxe-interior'), __('Procurement management','luxe-interior'), __('Site supervision & installation','luxe-interior'), __('Final styling & photography','luxe-interior') );
                    foreach ($items as $item) : ?>
                    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.6rem;">
                        <span style="color:var(--color-accent);font-size:0.75rem;">◆</span>
                        <span style="font-size:0.9rem;color:var(--color-muted);"><?php echo esc_html($item); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Service 2 -->
        <div style="display:grid;grid-template-columns:1fr 1fr;min-height:520px;margin-bottom:4px;">
            <div class="about-content" style="order:1;" data-reveal="left">
                <span class="eyebrow" style="display:block;margin-bottom:0.75rem;">02</span>
                <h2 style="font-size:2.2rem;"><?php esc_html_e('Commercial Interiors', 'luxe-interior'); ?></h2>
                <p style="margin-top:1.25rem;"><?php esc_html_e('We design offices, retail spaces, hospitality venues, and mixed-use developments that work beautifully for the people who occupy them — and for the businesses that depend on them.', 'luxe-interior'); ?></p>
                <p><?php esc_html_e('Commercial design requires a different lens: understanding footfall patterns, brand language, operational workflows, and the specific demands of each sector. We bring expertise across all of these, plus a design sensibility that elevates every environment we touch.', 'luxe-interior'); ?></p>
                <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:1.5rem;">
                    <?php foreach ( array('Offices','Restaurants','Retail','Hotels','Clinics','Showrooms') as $type ) : ?>
                    <span style="padding:0.4rem 1rem;background:var(--color-tag-bg);font-size:0.72rem;font-weight:500;letter-spacing:0.08em;text-transform:uppercase;color:var(--color-muted);"><?php echo esc_html($type); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div style="background:linear-gradient(225deg,#3D2B1A,#1A1410);display:flex;align-items:center;justify-content:center;order:2;" data-reveal="right">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                    <rect x="8" y="30" width="64" height="42" stroke="#B08D6A" stroke-width="1.5" fill="none"/>
                    <rect x="20" y="8" width="40" height="22" stroke="rgba(176,141,106,0.5)" stroke-width="1.5" fill="none"/>
                    <line x1="8" y1="50" x2="72" y2="50" stroke="rgba(176,141,106,0.3)" stroke-width="1"/>
                    <rect x="32" y="55" width="16" height="17" stroke="#B08D6A" stroke-width="1.5" fill="none"/>
                </svg>
            </div>
        </div>

        <!-- Service 3 -->
        <div style="display:grid;grid-template-columns:1fr 1fr;min-height:520px;margin-bottom:4px;">
            <div style="background:linear-gradient(135deg,#1C2420,#0F1A16);display:flex;align-items:center;justify-content:center;" data-reveal="left">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                    <circle cx="40" cy="40" r="6" fill="#B08D6A"/>
                    <circle cx="40" cy="40" r="18" stroke="#B08D6A" stroke-width="1.5" fill="none"/>
                    <line x1="40" y1="8" x2="40" y2="18" stroke="#B08D6A" stroke-width="1.5"/>
                    <line x1="40" y1="62" x2="40" y2="72" stroke="#B08D6A" stroke-width="1.5"/>
                    <line x1="8" y1="40" x2="18" y2="40" stroke="#B08D6A" stroke-width="1.5"/>
                    <line x1="62" y1="40" x2="72" y2="40" stroke="#B08D6A" stroke-width="1.5"/>
                    <line x1="17" y1="17" x2="24" y2="24" stroke="rgba(176,141,106,0.5)" stroke-width="1.5"/>
                    <line x1="56" y1="56" x2="63" y2="63" stroke="rgba(176,141,106,0.5)" stroke-width="1.5"/>
                    <line x1="63" y1="17" x2="56" y2="24" stroke="rgba(176,141,106,0.5)" stroke-width="1.5"/>
                    <line x1="24" y1="56" x2="17" y2="63" stroke="rgba(176,141,106,0.5)" stroke-width="1.5"/>
                </svg>
            </div>
            <div class="about-content" data-reveal="right">
                <span class="eyebrow" style="display:block;margin-bottom:0.75rem;">03</span>
                <h2 style="font-size:2.2rem;"><?php esc_html_e('Lighting Design', 'luxe-interior'); ?></h2>
                <p style="margin-top:1.25rem;"><?php esc_html_e('Lighting is the single most transformative element in any interior — and the most frequently misunderstood. Our dedicated lighting service creates layered, atmospheric schemes that serve both function and feeling.', 'luxe-interior'); ?></p>
                <p><?php esc_html_e('We develop complete lighting briefs including fixture specification, positioning, circuit design, and control systems. Every scheme is modeled in 3D before specification, so you see exactly how the light will fall before anything is ordered.', 'luxe-interior'); ?></p>
            </div>
        </div>

        <!-- Service 4 -->
        <div style="display:grid;grid-template-columns:1fr 1fr;min-height:520px;margin-bottom:4px;">
            <div class="about-content" data-reveal="left">
                <span class="eyebrow" style="display:block;margin-bottom:0.75rem;">04</span>
                <h2 style="font-size:2.2rem;"><?php esc_html_e('FF&amp;E Sourcing &amp; Procurement', 'luxe-interior'); ?></h2>
                <p style="margin-top:1.25rem;"><?php esc_html_e('Access to an extraordinary global network of furniture makers, fabric houses, lighting studios, and artisans — many of whom do not sell directly to the public.', 'luxe-interior'); ?></p>
                <p><?php esc_html_e('We handle every aspect of procurement: specifying, ordering, quality-controlling, and delivering furniture, fixtures, and equipment to your door. We manage lead times, coordinate deliveries, and supervise installation — so you do not have to.', 'luxe-interior'); ?></p>
            </div>
            <div style="background:linear-gradient(225deg,#241C10,#1A1208);display:flex;align-items:center;justify-content:center;" data-reveal="right">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                    <path d="M10 20 L40 8 L70 20 L70 60 L40 72 L10 60 Z" stroke="#B08D6A" stroke-width="1.5" fill="none"/>
                    <path d="M10 20 L40 32 L70 20" stroke="rgba(176,141,106,0.4)" stroke-width="1"/>
                    <line x1="40" y1="32" x2="40" y2="72" stroke="rgba(176,141,106,0.4)" stroke-width="1"/>
                </svg>
            </div>
        </div>

        <!-- Service 5 -->
        <div style="display:grid;grid-template-columns:1fr 1fr;min-height:520px;margin-bottom:4px;">
            <div style="background:linear-gradient(135deg,#201824,#140F1A);display:flex;align-items:center;justify-content:center;" data-reveal="left">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                    <rect x="12" y="12" width="24" height="24" stroke="#B08D6A" stroke-width="1.5" fill="none"/>
                    <rect x="44" y="12" width="24" height="24" stroke="rgba(176,141,106,0.5)" stroke-width="1.5" fill="none"/>
                    <rect x="12" y="44" width="24" height="24" stroke="rgba(176,141,106,0.5)" stroke-width="1.5" fill="none"/>
                    <rect x="44" y="44" width="24" height="24" stroke="#B08D6A" stroke-width="1.5" fill="none"/>
                    <line x1="36" y1="24" x2="44" y2="24" stroke="#B08D6A" stroke-width="1.5"/>
                    <line x1="24" y1="36" x2="24" y2="44" stroke="#B08D6A" stroke-width="1.5"/>
                    <line x1="56" y1="36" x2="56" y2="44" stroke "#B08D6A" stroke-width="1.5"/>
                    <line x1="36" y1="56" x2="44" y2="56" stroke="#B08D6A" stroke-width="1.5"/>
                </svg>
            </div>
            <div class="about-content" data-reveal="right">
                <span class="eyebrow" style="display:block;margin-bottom:0.75rem;">05</span>
                <h2 style="font-size:2.2rem;"><?php esc_html_e('Space Planning &amp; Consultation', 'luxe-interior'); ?></h2>
                <p style="margin-top:1.25rem;"><?php esc_html_e('Not every project requires a full design engagement. Our consultation service provides expert space planning advice, mood board direction, and furniture guidance — ideal for those who want professional input but prefer to manage execution themselves.', 'luxe-interior'); ?></p>
                <p><?php esc_html_e('Sessions are available in-studio or on-site. We provide a written design brief and annotated floor plan following each consultation.', 'luxe-interior'); ?></p>
                <a href="<?php echo esc_url( home_url('/book-consultation') ); ?>" class="btn" style="margin-top:1.5rem;"><?php esc_html_e('Book a Consultation', 'luxe-interior'); ?></a>
            </div>
        </div>

    </div>
</section>

<!-- PROCESS -->
<section style="background:var(--color-charcoal);">
    <div class="container">
        <div style="text-align:center;margin-bottom:var(--space-lg);" data-reveal>
            <span class="eyebrow" style="color:var(--color-accent);display:block;margin-bottom:1rem;"><?php esc_html_e('How It Works', 'luxe-interior'); ?></span>
            <h2 style="color:#fff;"><?php esc_html_e('Our Design Process', 'luxe-interior'); ?></h2>
        </div>
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:0;position:relative;">
            <div style="position:absolute;top:28px;left:10%;right:10%;height:1px;background:linear-gradient(to right,transparent,rgba(176,141,106,0.4),transparent);z-index:0;"></div>
            <?php $steps = array(
                array('num'=>'01','title'=>__('Discovery','luxe-interior'),'desc'=>__('We listen deeply — to your lifestyle, aspirations, and the space itself.','luxe-interior')),
                array('num'=>'02','title'=>__('Concept','luxe-interior'),'desc'=>__('A clear design direction presented through mood boards and spatial sketches.','luxe-interior')),
                array('num'=>'03','title'=>__('Design Development','luxe-interior'),'desc'=>__('Detailed floor plans, material samples, and furniture selections.','luxe-interior')),
                array('num'=>'04','title'=>__('Procurement','luxe-interior'),'desc'=>__('We manage all ordering, lead times, deliveries, and supplier relationships.','luxe-interior')),
                array('num'=>'05','title'=>__('Installation','luxe-interior'),'desc'=>__('Our team oversees every aspect of installation and final styling.','luxe-interior')),
            );
            foreach ($steps as $i => $step) : ?>
            <div style="padding:0 1.5rem;text-align:center;position:relative;z-index:1;" data-reveal>
                <div style="width:56px;height:56px;border:1.5px solid var(--color-accent);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;background:var(--color-charcoal);">
                    <span style="font-family:var(--font-body);font-size:0.7rem;font-weight:600;letter-spacing:0.1em;color:var(--color-accent);"><?php echo esc_html($step['num']); ?></span>
                </div>
                <h5 style="color:#fff;margin-bottom:0.75rem;font-size:0.85rem;"><?php echo esc_html($step['title']); ?></h5>
                <p style="font-size:0.82rem;color:rgba(255,255,255,0.45);line-height:1.6;"><?php echo esc_html($step['desc']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- PRICING -->
<section>
    <div class="container">
        <div style="text-align:center;margin-bottom:var(--space-lg);" data-reveal>
            <span class="eyebrow" style="display:block;margin-bottom:1rem;"><?php esc_html_e('Investment', 'luxe-interior'); ?></span>
            <h2><?php esc_html_e('Service Tiers', 'luxe-interior'); ?></h2>
            <p style="max-width:560px;margin-inline:auto;margin-top:1rem;"><?php esc_html_e('Every project is quoted individually based on scope and scale. These tiers give you a starting point for conversation.', 'luxe-interior'); ?></p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;">
            <?php $plans = array(
                array(
                    'name'     => __('Consultation', 'luxe-interior'),
                    'price'    => '৳15,000',
                    'unit'     => __('per session', 'luxe-interior'),
                    'desc'     => __('Ideal for those who want expert direction before making decisions independently.', 'luxe-interior'),
                    'features' => array(
                        __('2-hour on-site session', 'luxe-interior'),
                        __('Space planning guidance', 'luxe-interior'),
                        __('Written design brief', 'luxe-interior'),
                        __('Annotated floor plan', 'luxe-interior'),
                        __('Follow-up email support (48hrs)', 'luxe-interior'),
                    ),
                    'cta'      => __('Book a Session', 'luxe-interior'),
                    'featured' => false,
                ),
                array(
                    'name'     => __('Full Design', 'luxe-interior'),
                    'price'    => 'From ৳1.5L',
                    'unit'     => __('per room', 'luxe-interior'),
                    'desc'     => __('Our comprehensive end-to-end service for residential and commercial spaces.', 'luxe-interior'),
                    'features' => array(
                        __('Full concept & design development', 'luxe-interior'),
                        __('3D visualization', 'luxe-interior'),
                        __('Complete FF&E specification', 'luxe-interior'),
                        __('Procurement management', 'luxe-interior'),
                        __('Site supervision', 'luxe-interior'),
                        __('Final styling & photography', 'luxe-interior'),
                    ),
                    'cta'      => __('Start a Project', 'luxe-interior'),
                    'featured' => true,
                ),
                array(
                    'name'     => __('Project Management', 'luxe-interior'),
                    'price'    => 'Custom',
                    'unit'     => __('based on scope', 'luxe-interior'),
                    'desc'     => __('For clients who have their own designer but need expert project management on site.', 'luxe-interior'),
                    'features' => array(
                        __('Contractor coordination', 'luxe-interior'),
                        __('Procurement oversight', 'luxe-interior'),
                        __('Weekly site visits', 'luxe-interior'),
                        __('Quality control', 'luxe-interior'),
                        __('Timeline management', 'luxe-interior'),
                    ),
                    'cta'      => __('Discuss Your Project', 'luxe-interior'),
                    'featured' => false,
                ),
            );
            foreach ($plans as $plan) : ?>
            <div style="border:<?php echo $plan['featured'] ? '2px solid var(--color-accent)' : '1px solid var(--color-border)'; ?>;padding:2.5rem;position:relative;" data-reveal>
                <?php if ($plan['featured']) : ?>
                <div style="position:absolute;top:-1px;left:50%;transform:translateX(-50%);background:var(--color-accent);color:#fff;font-size:0.65rem;font-weight:600;letter-spacing:0.15em;text-transform:uppercase;padding:0.3rem 1.25rem;">
                    <?php esc_html_e('Most Popular', 'luxe-interior'); ?>
                </div>
                <?php endif; ?>
                <h4 style="font-family:var(--font-body);font-size:0.75rem;font-weight:600;letter-spacing:0.15em;text-transform:uppercase;color:var(--color-muted);margin-bottom:1rem;"><?php echo esc_html($plan['name']); ?></h4>
                <div style="font-family:var(--font-display);font-size:2.8rem;color:var(--color-charcoal);line-height:1;"><?php echo esc_html($plan['price']); ?></div>
                <div style="font-size:0.75rem;color:var(--color-muted);margin-bottom:1.25rem;margin-top:0.25rem;"><?php echo esc_html($plan['unit']); ?></div>
                <p style="font-size:0.85rem;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid var(--color-border);"><?php echo esc_html($plan['desc']); ?></p>
                <?php foreach ($plan['features'] as $feat) : ?>
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.65rem;">
                    <span style="color:var(--color-accent);font-size:0.6rem;flex-shrink:0;">◆</span>
                    <span style="font-size:0.85rem;color:var(--color-muted);"><?php echo esc_html($feat); ?></span>
                </div>
                <?php endforeach; ?>
                <a href="<?php echo esc_url( home_url('/book-consultation') ); ?>" class="btn <?php echo $plan['featured'] ? 'btn-accent' : ''; ?>" style="width:100%;justify-content:center;margin-top:2rem;display:flex;">
                    <?php echo esc_html($plan['cta']); ?>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA BAND -->
<div style="background:var(--color-accent);padding:5rem 0;text-align:center;">
    <div class="container">
        <h2 style="color:#fff;margin-bottom:1rem;" data-reveal><?php esc_html_e('Ready to Transform Your Space?', 'luxe-interior'); ?></h2>
        <p style="color:rgba(255,255,255,0.75);max-width:480px;margin-inline:auto;margin-bottom:2rem;" data-reveal>
            <?php esc_html_e('Every great interior begins with a single conversation. Tell us about your project.', 'luxe-interior'); ?>
        </p>
        <a href="<?php echo esc_url( home_url('/book-consultation') ); ?>" class="btn btn-ghost" data-reveal>
            <?php esc_html_e('Book a Free Consultation', 'luxe-interior'); ?>
        </a>
    </div>
</div>

<?php get_footer(); ?>
