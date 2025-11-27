-- Global Insights News Website Database Schema
-- MySQL 5.7+ / MariaDB 10.2+
-- Charset: utf8mb4 for full Unicode support

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Create Database
CREATE DATABASE IF NOT EXISTS global_insights CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE global_insights;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS users_admin (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tags Table
CREATE TABLE IF NOT EXISTS tags (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Articles Table
CREATE TABLE IF NOT EXISTS articles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    slug VARCHAR(255) UNIQUE NOT NULL,
    body LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    category_id INT UNSIGNED,
    author VARCHAR(100) DEFAULT 'Admin',
    status ENUM('draft', 'published', 'archived') DEFAULT 'published',
    is_featured TINYINT(1) DEFAULT 0,
    views INT UNSIGNED DEFAULT 0,
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    meta_description VARCHAR(160),
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_category (category_id),
    INDEX idx_published (published_at),
    INDEX idx_featured (is_featured),
    FULLTEXT idx_search (title, subtitle, body),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Article-Tag Junction Table
CREATE TABLE IF NOT EXISTS article_tag (
    article_id INT UNSIGNED NOT NULL,
    tag_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Likes Table
CREATE TABLE IF NOT EXISTS likes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id INT UNSIGNED NOT NULL,
    identifier VARCHAR(255) NOT NULL COMMENT 'IP address or cookie hash',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_article (article_id),
    INDEX idx_identifier (identifier),
    UNIQUE KEY unique_like (article_id, identifier),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comments Table
CREATE TABLE IF NOT EXISTS comments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) DEFAULT 'Anonymous',
    content TEXT NOT NULL,
    ip_address VARCHAR(45),
    status ENUM('pending', 'approved', 'rejected', 'spam') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_article (article_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Images Table
CREATE TABLE IF NOT EXISTS images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    path VARCHAR(255) NOT NULL,
    size INT UNSIGNED,
    mime_type VARCHAR(50),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings Table
CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- View Tracking Table (prevent duplicate views per IP/day)
CREATE TABLE IF NOT EXISTS article_views (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id INT UNSIGNED NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    viewed_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_view (article_id, ip_address, viewed_date),
    INDEX idx_article (article_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rate Limiting Table
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    attempt_count INT UNSIGNED DEFAULT 1,
    last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ip_action (ip_address, action_type),
    INDEX idx_last_attempt (last_attempt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Admin User (username: admin, password: Admin@123456)
-- Password hash for 'Admin@123456'
INSERT INTO users_admin (username, password_hash, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@globalinsights.com');

-- Insert Sample Categories
INSERT INTO categories (name, slug, description) VALUES
('World News', 'world-news', 'Global news and international affairs'),
('Technology', 'technology', 'Latest tech news and innovations'),
('Business', 'business', 'Business and finance news'),
('Sports', 'sports', 'Sports news and updates'),
('Entertainment', 'entertainment', 'Entertainment and celebrity news'),
('Health', 'health', 'Health and wellness news'),
('Science', 'science', 'Scientific discoveries and research'),
('Politics', 'politics', 'Political news and analysis');

-- Insert Sample Tags
INSERT INTO tags (name, slug) VALUES
('Breaking News', 'breaking-news'),
('Analysis', 'analysis'),
('Opinion', 'opinion'),
('Featured', 'featured'),
('Trending', 'trending'),
('COVID-19', 'covid-19'),
('Climate Change', 'climate-change'),
('AI & Machine Learning', 'ai-machine-learning'),
('Cryptocurrency', 'cryptocurrency'),
('Space Exploration', 'space-exploration');

-- Insert Sample Articles
INSERT INTO articles (title, subtitle, slug, body, featured_image, category_id, author, status, is_featured, meta_description, published_at) VALUES
(
    'Global Climate Summit 2025 Reaches Historic Agreement',
    'World leaders commit to ambitious carbon reduction targets',
    'global-climate-summit-2025-historic-agreement',
    '<p>In a landmark decision that could reshape the future of our planet, world leaders at the Global Climate Summit 2025 have reached a historic agreement on carbon emissions reduction. The agreement, signed by 195 countries, sets ambitious targets to limit global temperature rise to 1.5 degrees Celsius above pre-industrial levels.</p>
    <p>The accord includes binding commitments from major economies to achieve net-zero emissions by 2050, with interim targets set for 2030 and 2040. Developed nations have pledged $500 billion in climate finance to help developing countries transition to renewable energy sources.</p>
    <p>"This is a defining moment for humanity," said UN Secretary-General. "We have shown that when the world comes together, we can tackle even the most daunting challenges."</p>
    <p>Key provisions of the agreement include:</p>
    <ul>
        <li>Mandatory phase-out of coal power by 2035 in developed nations</li>
        <li>Investment in renewable energy infrastructure</li>
        <li>Protection of forests and ocean ecosystems</li>
        <li>Technology transfer to developing nations</li>
    </ul>
    <p>Environmental activists have cautiously welcomed the agreement while emphasizing the need for immediate action to meet the stated goals.</p>',
    'climate-summit.jpg',
    1,
    'Sarah Johnson',
    'published',
    1,
    'World leaders reach historic climate agreement at Global Climate Summit 2025 with ambitious carbon reduction targets.',
    '2025-11-27 10:00:00'
),
(
    'Breakthrough in Quantum Computing: 1000-Qubit Processor Unveiled',
    'Tech giant announces quantum leap in computational power',
    'quantum-computing-1000-qubit-processor',
    '<p>In a major technological breakthrough, scientists have successfully developed and tested a 1000-qubit quantum processor, marking a significant milestone in the race toward practical quantum computing.</p>
    <p>The new processor, developed by a leading tech company, demonstrates unprecedented stability and error correction capabilities, bringing quantum computers one step closer to solving real-world problems that are impossible for classical computers.</p>
    <p>"This represents a quantum leap forward," said Dr. Emily Chen, lead researcher on the project. "We are now at the threshold of quantum advantage for practical applications."</p>
    <p>Potential applications include:</p>
    <ul>
        <li>Drug discovery and molecular simulation</li>
        <li>Cryptography and cybersecurity</li>
        <li>Financial modeling and optimization</li>
        <li>Artificial intelligence and machine learning</li>
    </ul>
    <p>The technology is expected to revolutionize industries ranging from healthcare to finance within the next decade.</p>',
    'quantum-computer.jpg',
    2,
    'Michael Rodriguez',
    'published',
    1,
    'Scientists unveil groundbreaking 1000-qubit quantum processor with unprecedented computational power.',
    '2025-11-26 14:30:00'
),
(
    'Global Economy Shows Strong Recovery in Q4 2025',
    'GDP growth exceeds expectations across major economies',
    'global-economy-recovery-q4-2025',
    '<p>The global economy has demonstrated remarkable resilience in the fourth quarter of 2025, with GDP growth exceeding analyst expectations across major economies. The recovery is driven by robust consumer spending, technological innovation, and strategic government investments.</p>
    <p>According to the International Monetary Fund (IMF), global GDP growth reached 4.2% in Q4, surpassing the projected 3.5%. Major economies including the United States, European Union, and Asian markets all reported positive growth figures.</p>
    <p>Key factors contributing to the recovery include:</p>
    <ul>
        <li>Increased consumer confidence and spending</li>
        <li>Strong corporate earnings and business investment</li>
        <li>Government infrastructure projects</li>
        <li>Technological sector expansion</li>
    </ul>
    <p>Economists remain cautiously optimistic about 2026, though concerns about inflation and geopolitical tensions persist.</p>',
    'economy-growth.jpg',
    3,
    'David Thompson',
    'published',
    0,
    'Global economy shows strong recovery in Q4 2025 with GDP growth exceeding expectations.',
    '2025-11-25 09:15:00'
),
(
    'World Cup 2026 Preparations Enter Final Phase',
    'Host cities unveil state-of-the-art stadiums and facilities',
    'world-cup-2026-preparations-final-phase',
    '<p>With less than a year to go before the FIFA World Cup 2026, host cities across North America are putting the finishing touches on state-of-the-art stadiums and infrastructure projects that promise to deliver an unforgettable tournament.</p>
    <p>The tournament, which will be jointly hosted by the United States, Canada, and Mexico, will feature 48 teams competing across 16 cities. New stadiums in Los Angeles, Mexico City, and Toronto showcase cutting-edge sustainable design and technology.</p>
    <p>"We are creating venues that will leave a lasting legacy for communities long after the final whistle," said FIFA President.</p>
    <p>Tournament highlights include:</p>
    <ul>
        <li>48-team format for the first time</li>
        <li>104 matches across three countries</li>
        <li>Sustainable stadium design with solar power</li>
        <li>Advanced fan experience technology</li>
    </ul>
    <p>Ticket sales have already broken records, with millions of fans expected to attend matches across the region.</p>',
    'world-cup-stadium.jpg',
    4,
    'Carlos Martinez',
    'published',
    0,
    'FIFA World Cup 2026 preparations reach final phase with unveiling of state-of-the-art stadiums.',
    '2025-11-24 16:45:00'
),
(
    'New Blockbuster Film Breaks Opening Weekend Records',
    'Sci-fi epic "Starbound" dominates box office worldwide',
    'blockbuster-film-starbound-breaks-records',
    '<p>The highly anticipated sci-fi epic "Starbound" has shattered opening weekend box office records, earning an unprecedented $425 million globally. The film, directed by acclaimed filmmaker James Cameron, combines stunning visual effects with a compelling storyline about humanity\'s first interstellar journey.</p>
    <p>Critics have praised the film\'s groundbreaking use of virtual production technology and its emotional depth. "Starbound" features an ensemble cast and explores themes of exploration, sacrifice, and the human spirit.</p>
    <p>"This is cinema at its finest," wrote one leading film critic. "A perfect blend of spectacle and substance that will resonate with audiences for generations."</p>
    <p>The film\'s success demonstrates the continued appetite for theatrical experiences and high-quality storytelling in the streaming age.</p>',
    'starbound-movie.jpg',
    5,
    'Jennifer Lee',
    'published',
    0,
    'Sci-fi blockbuster Starbound breaks opening weekend box office records with $425 million worldwide.',
    '2025-11-23 11:20:00'
),
(
    'Revolutionary Cancer Treatment Shows 90% Success Rate',
    'New immunotherapy approach transforms cancer care',
    'revolutionary-cancer-treatment-success',
    '<p>Medical researchers have announced breakthrough results from a Phase III clinical trial of a revolutionary cancer treatment that combines advanced immunotherapy with targeted drug delivery. The treatment has shown a remarkable 90% success rate in treating previously difficult-to-treat cancers.</p>
    <p>The therapy, developed over a decade of research, trains the patient\'s own immune system to recognize and destroy cancer cells while minimizing damage to healthy tissue. The treatment has shown particular effectiveness against melanoma, lung cancer, and certain blood cancers.</p>
    <p>"This represents a paradigm shift in cancer treatment," said Dr. Rebecca Foster, lead oncologist. "We are witnessing what could be the beginning of the end for many forms of cancer."</p>
    <p>Key benefits include:</p>
    <ul>
        <li>Minimal side effects compared to traditional chemotherapy</li>
        <li>Shorter treatment duration</li>
        <li>Higher long-term survival rates</li>
        <li>Potential application to multiple cancer types</li>
    </ul>
    <p>The treatment is expected to receive regulatory approval within the next 12 months and will be made available to patients worldwide.</p>',
    'cancer-treatment.jpg',
    6,
    'Dr. Amanda Roberts',
    'published',
    1,
    'Revolutionary cancer treatment shows 90% success rate in clinical trials, transforming cancer care.',
    '2025-11-22 08:00:00'
),
(
    'SpaceX Successfully Lands Crew on Mars',
    'Historic mission marks humanity\'s first steps on the Red Planet',
    'spacex-mars-landing-historic-mission',
    '<p>In a historic achievement for space exploration, SpaceX has successfully landed a crew of six astronauts on Mars, marking humanity\'s first footsteps on the Red Planet. The Ares I mission touched down in Jezero Crater after a seven-month journey from Earth.</p>
    <p>"That\'s one small step for a person, one giant leap for humanity\'s future as a multi-planetary species," declared Commander Sarah Mitchell as she became the first human to walk on Martian soil.</p>
    <p>The mission represents the culmination of decades of preparation and technological advancement. The crew will spend 18 months conducting scientific research, testing life support systems, and preparing infrastructure for future missions.</p>
    <p>Mission objectives include:</p>
    <ul>
        <li>Search for evidence of past or present life</li>
        <li>Study Martian geology and climate</li>
        <li>Test technologies for sustained human presence</li>
        <li>Establish preliminary base infrastructure</li>
    </ul>
    <p>NASA and international partners are already planning follow-up missions, with the goal of establishing a permanent Mars base by 2035.</p>',
    'mars-landing.jpg',
    7,
    'Robert Chang',
    'published',
    1,
    'SpaceX successfully lands crew on Mars in historic mission, marking humanity\'s first steps on the Red Planet.',
    '2025-11-21 13:30:00'
),
(
    'Major Political Reforms Announced Following Historic Election',
    'New government promises transparency and democratic renewal',
    'major-political-reforms-historic-election',
    '<p>Following a landmark election that saw record voter turnout, the newly elected government has announced a comprehensive package of political reforms aimed at strengthening democratic institutions and increasing government transparency.</p>
    <p>The reforms, which enjoy broad cross-party support, include measures to reduce the influence of money in politics, strengthen anti-corruption mechanisms, and enhance citizen participation in governance.</p>
    <p>"Democracy must evolve to meet the challenges of the 21st century," stated Prime Minister. "These reforms will ensure that government truly serves the people."</p>
    <p>Key reform measures:</p>
    <ul>
        <li>Campaign finance reform and donation limits</li>
        <li>Enhanced transparency in government operations</li>
        <li>Digital platforms for citizen engagement</li>
        <li>Independent oversight bodies</li>
    </ul>
    <p>Political analysts view the reforms as a potential model for other democracies facing similar challenges of public trust and institutional integrity.</p>',
    'political-reform.jpg',
    8,
    'Alexander Wright',
    'published',
    0,
    'New government announces major political reforms following historic election with record turnout.',
    '2025-11-20 10:45:00'
),
(
    'AI Assistant Passes Turing Test Convincingly',
    'Advanced language model demonstrates human-level conversation',
    'ai-assistant-passes-turing-test',
    '<p>In a significant milestone for artificial intelligence, an advanced AI assistant has passed the Turing Test convincingly, demonstrating the ability to engage in human-level conversation across a wide range of topics. The achievement marks a new chapter in the development of artificial general intelligence.</p>
    <p>During extensive testing, human evaluators were unable to distinguish the AI\'s responses from those of human participants more than 70% of the time. The AI demonstrated not only factual knowledge but also emotional intelligence, humor, and creative thinking.</p>
    <p>"This is not about replacing human intelligence but augmenting it," explained Dr. Lisa Wong, AI research director. "These systems can become powerful tools for education, creativity, and problem-solving."</p>
    <p>The technology raises important questions about AI ethics, regulation, and the future relationship between humans and intelligent machines. Researchers emphasize the importance of developing AI systems that are aligned with human values and beneficial to society.</p>',
    'ai-turing-test.jpg',
    2,
    'Thomas Anderson',
    'published',
    0,
    'Advanced AI assistant passes Turing Test convincingly, demonstrating human-level conversation abilities.',
    '2025-11-19 15:10:00'
),
(
    'Cryptocurrency Market Reaches New Milestone',
    'Bitcoin surpasses $150,000 as institutional adoption grows',
    'cryptocurrency-market-new-milestone',
    '<p>The cryptocurrency market has reached a new milestone as Bitcoin surpassed $150,000 for the first time, driven by increasing institutional adoption and growing acceptance of digital currencies in mainstream finance.</p>
    <p>Major financial institutions have launched cryptocurrency services, and several countries have begun integrating digital currencies into their monetary systems. The market\'s total capitalization now exceeds $5 trillion.</p>
    <p>"We\'re witnessing the maturation of the cryptocurrency market," said financial analyst Mark Stevens. "What was once considered fringe is now becoming an integral part of the global financial system."</p>
    <p>Key developments include:</p>
    <ul>
        <li>Major banks offering crypto custody services</li>
        <li>Central bank digital currencies (CBDCs) launching</li>
        <li>Improved regulatory frameworks</li>
        <li>Enhanced security and infrastructure</li>
    </ul>
    <p>Despite the growth, experts caution investors about volatility and the importance of understanding the risks associated with cryptocurrency investments.</p>',
    'cryptocurrency-bitcoin.jpg',
    3,
    'Michelle Park',
    'published',
    0,
    'Bitcoin surpasses $150,000 as cryptocurrency market reaches new milestone with growing institutional adoption.',
    '2025-11-18 12:00:00'
);

-- Link articles to tags
INSERT INTO article_tag (article_id, tag_id) VALUES
(1, 1), (1, 7), -- Climate article: Breaking News, Climate Change
(2, 1), (2, 4), (2, 8), -- Quantum: Breaking News, Featured, AI & ML
(3, 2), -- Economy: Analysis
(4, 5), -- Sports: Trending
(5, 5), -- Entertainment: Trending
(6, 1), (6, 4), -- Cancer: Breaking News, Featured
(7, 1), (7, 4), (7, 10), -- Mars: Breaking News, Featured, Space
(8, 2), (8, 3), -- Politics: Analysis, Opinion
(9, 8), (9, 5), -- AI: AI & ML, Trending
(10, 9), (10, 5); -- Crypto: Cryptocurrency, Trending

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Global Insights'),
('site_tagline', 'Your Source for World News'),
('articles_per_page', '12'),
('comments_auto_approve', '0'),
('site_email', 'contact@globalinsights.com'),
('timezone', 'UTC');

-- Sample likes (for demonstration)
INSERT INTO likes (article_id, identifier) VALUES
(1, 'ip_192.168.1.100'), (1, 'ip_192.168.1.101'), (1, 'ip_192.168.1.102'),
(2, 'ip_192.168.1.100'), (2, 'ip_192.168.1.103'),
(3, 'ip_192.168.1.104'),
(6, 'ip_192.168.1.100'), (6, 'ip_192.168.1.101'), (6, 'ip_192.168.1.102'), (6, 'ip_192.168.1.103'),
(7, 'ip_192.168.1.100'), (7, 'ip_192.168.1.101'), (7, 'ip_192.168.1.102');

-- Sample comments
INSERT INTO comments (article_id, name, content, status, ip_address) VALUES
(1, 'John Doe', 'This is a great step forward for our planet! We need more action like this.', 'approved', '192.168.1.100'),
(1, 'Jane Smith', 'I hope world leaders will actually follow through on these commitments.', 'approved', '192.168.1.101'),
(2, 'Tech Enthusiast', 'Quantum computing is going to revolutionize everything we know about technology!', 'approved', '192.168.1.102'),
(6, 'Hope Wilson', 'This is amazing news for cancer patients worldwide. Science is incredible!', 'approved', '192.168.1.103'),
(7, 'Space Fan', 'Historic moment! Can\'t believe we\'re finally on Mars!', 'approved', '192.168.1.104');

-- End of Schema
