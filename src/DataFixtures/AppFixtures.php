<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\File;
use App\Entity\MetaCategory;
use App\Entity\Resource;
use App\Entity\MetaAccessLevel;
use App\Entity\Comment;
use App\Entity\MetaDocumentType;
use App\Entity\MetaExtension;
use App\Entity\MetaKeyword;
use App\Entity\MetaPurpose;
use App\Entity\MetaMedia;
use App\Entity\User;
use App\Entity\UserAccess;
use App\Entity\UserGroup;
use App\Form\Traits\ResourceLanguages;
use App\Utils\FileUploader;
use App\Utils\Slugger;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    use ResourceLanguages;

    private $passwordEncoder;
    private $fileUploader;
    private $references = [];
    private $languages = [];

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        FileUploader $fileUploader
    )
    {
        $this->fileUploader = $fileUploader;
        $this->passwordEncoder = $passwordEncoder;
        $this->languages = array_keys($this->getLanguages());
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadAccessLevels($manager);
        $this->loadDocumentTypes($manager);

        $this->loadUserGroups($manager);
        $this->loadUserAccesses($manager);
        $this->loadUsers($manager);

        $this->loadKeywords($manager);
        $this->loadMediaTypes($manager);
        $this->loadExtensions($manager);
        $this->loadPurposes($manager);
        $this->loadCategories($manager);
        $this->loadFiles($manager);
        $this->loadResources($manager);
    }

    private function loadUserGroups(ObjectManager $manager): void
    {
        foreach ($this->getUserGroups() as $name) {
            $userGroup = new UserGroup();
            $userGroup->setName($name)
                ->setDocumentTypes([$this->getRandomDocument()])
            ;
            $manager->persist($userGroup);
            $ref = 'group-' . $this->_slug($name);
            $this->references[UserGroup::class][] = $ref;
            $this->addReference($ref, $userGroup);
        }

        $manager->flush();
    }

    private function loadUserAccesses(ObjectManager $manager): void
    {
        foreach ($this->getUserAccesses() as $name) {
            $userAccess = new UserAccess();
            $userAccess->setName($name)
                ->setAccessLevel($this->getRandomAccessLevel())
            ;
            $manager->persist($userAccess);
            $ref = 'access-' . $this->_slug($name);
            $this->references[UserAccess::class][] = $ref;
            $this->addReference($ref, $userAccess);
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$fullname, $username, $password, $email, $roles]) {
            $user = new User();
            $user->setFullName($fullname);
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);
            $user->setGroup($this->getRandomUserGroup());
            $user->setAccess($this->getRandomFromClassName(UserAccess::class));

            $manager->persist($user);
            $this->addReference($username, $user);
        }

        $manager->flush();
    }

    private function loadKeywords(ObjectManager $manager): void
    {
        foreach ($this->getKeywordData() as $index => $name) {
            $keyword = new MetaKeyword();
            $keyword->setName($name);

            $manager->persist($keyword);
            $this->addReference('keyword-' . $name, $keyword);
        }

        $manager->flush();
    }

    private function loadResources(ObjectManager $manager): void
    {
        foreach ($this->getResourceData() as [$title,
                 $slug,
                 $summary,
                 $content, $publishedAt, $author]
        ) {


            /** @var MetaMedia $randomMedia */
            $randomMedia = $this->getRandomMedia();
            $extension = $randomMedia->getExtensions()->next()
                ? $randomMedia->getExtensions()->current()
                : $randomMedia->getExtensions()->first();


            $resource = new Resource();
            $resource->setTitle($title)
                ->setAnnotation($summary)
                ->setPublishedAt($publishedAt)
                ->setAuthor($author)
                ->addKeyword(...$this->getRandomKeywords())
                ->setPurpose($this->getRandomPurpose())
                ->setDocumentType($this->getRandomDocument())
                ->setAccessLevel($this->getRandomAccessLevel())
                ->setMediaType($randomMedia)
                ->setExtension($extension)
                ->setSource('source')
                ->setTheme('theme')
                ->setLanguage($this->getRandomLanguage())
                ->setCategory($this->getRandomCategory())
            ;

            if ($this->hasReference('file-' . $extension)) {
                /** @var File $file */
                $file = $this->getReference('file-' . $extension);
                $resource
                    ->setFile($file)
                    ->setSize($file->getSize())
                ;
            }

            foreach (range(1, 5) as $i) {
                $comment = new Comment();
                $comment->setAuthor($this->getReference('john_user'));
                $comment->setContent($this->getRandomText(random_int(255, 512)));
                $comment->setPublishedAt(new \DateTime('now + ' . $i . 'seconds'));
                $resource->addComment($comment);
            }

            $manager->persist($resource);
        }

        $manager->flush();
    }

    private function loadMediaTypes(ObjectManager $manager): void
    {
        foreach ($this->getExtensionData() as $type => $extensions) {

            $object = new MetaMedia();
            $object->setName($type);
            $manager->persist($object);
            $this->addReference('media-' . $type, $object);
        }
    }

    private function loadExtensions(ObjectManager $manager): void
    {
        foreach ($this->getExtensionData() as $type => $extensions) {

            foreach ($extensions as $name) {

                $extension = new MetaExtension();
                $extension->setName($name);

                $manager->persist($extension);

                /** @var MetaMedia $mediaType */
                $mediaType = $this->getReference('media-' . $type);
                $mediaType->addExtension($extension);
            }
        }

        $manager->flush();
    }

    private function loadDocumentTypes(ObjectManager $manager): void
    {
        foreach ($this->getDocumentTypeData() as $datum) {

            $object = new MetaDocumentType();
            $object->setName($datum);
            $manager->persist($object);
            $this->addReference('document-' . $datum, $object);
        }

        $manager->flush();
    }

    private function loadPurposes(ObjectManager $manager): void
    {
        foreach ($this->getPurposeData() as $datum) {

            $object = new MetaPurpose();
            $object->setName($datum);
            $manager->persist($object);
            $this->addReference('purpose-' . $datum, $object);
        }

        $manager->flush();
    }

    private function getDocumentTypeData(): array
    {
        /** @see https://studopedia.info/5-63959.html */
        return [
            //1. Организационные документы
            'должностные инструкции', 'правила внутреннего трудового распорядка',
            //2. розпорядчу
            'наказ', 'рішення', 'вказівка', 'розпорядження',
            //3. довідково-інформаційну
            'акт', 'протокол', 'огляд', 'лист', 'доповідна записка', 'пояснювальна записка', 'довідка', 'звiт', 'характеристика',
            //4. Документы по личному составу –
            'приказы по личному составу', 'заявления', 'личные карточки', 'автобиографии', 'резюме',
        ];
    }

    private function getCategoryData(): array
    {

        return [
            'Організація навчання' => [
                'Стандарти' => [
                    'Державні',
                    'Університетські'
                ],
                'Освітні програми' => [
                    'Державні',
                    'Університетські',
                    'Закордонні',
                ],
                'Навчальні плани', 'Розклади занять',
            ],

            'Методичне забезпечення' => [
                'Документи деканату',
                'Документи кафедри',
                'Документи зі спеціальностей',
                'Документи зі спеціалізацій',
            ],

            'Освітній процес' => [
                'Навчальні заняття',
                'Самостійні роботи',
                'Практики',
            ],

            'Якість освіти' => [
                'Стандарти',
                'Методичне забезпечення',
                'Контроль',
                'Рейтинги',
                'Атестація',
            ],

            'Переривання навчання' => [
                'Відрахування',
                'Переривання',
                'Повторне навчання',
                'Переведення',
                'Поновлення',
            ],

            'Інклюзивне навчання',
            'Кураторство',
            'Міжнародна освіта'
        ];
    }

    protected function _createNode($key, $value, ObjectManager $manager, string $ref, string $className, $parent = null)
    {

        if (isset($key)) {

            /** @var MetaCategory|MetaAccessLevel $node */
            $node = new $className();
            $node->setName(is_string($key) ? $key : $value);

            $ref .= '-' . $this->_slug($node->getName());

            if ($parent) {
                $node->setParent($parent);
            }

            $this->addReference($ref, $node);
            $this->references[$className][] = $ref;
            $manager->persist($node);

        } else {
            $node = null;
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $this->_createNode($k, $v, $manager, $ref, $className, $node);
            }
        }
    }

    private function loadFiles(ObjectManager $manager): void
    {
        foreach ($this->getExtensionData() as $type => $extensions) {
            foreach ($extensions as $extension) {

                $fileName = 'test.' . $extension;
                $filePath = implode(DIRECTORY_SEPARATOR, [
                    dirname(dirname(dirname($this->fileUploader->getTargetDirectory()))),
                    'data',
                    'fixtures',
                    $fileName
                ]);

                $file = new File();
                $file->setExtension($extension)
                    ->setSize(filesize($filePath))
                    ->setFileName($fileName)
                    ->setUpload($fileName)
                ;

                if (!$this->fileUploader->exists($file)) {

                    $filesystem = new Filesystem();
                    $filesystem->copy(
                        $filePath,
                        $this->fileUploader->getTargetExtensionDirectory($extension) . DIRECTORY_SEPARATOR . $fileName
                    );
                }

                $manager->persist($file);
                $ref = 'file-' . $extension;
                $this->addReference($ref, $file);
                $this->references[File::class][] = $ref;
            }
        }

        $manager->flush();

    }

    private function loadAccessLevels(ObjectManager $manager): void
    {
        $this->_createNode(null, $this->getAccessLevelData(), $manager, 'access', MetaAccessLevel::class);

        $manager->flush();
    }

    private function loadCategories(ObjectManager $manager): void
    {

        $this->_createNode(null, $this->getCategoryData(), $manager, 'category', MetaCategory::class);

        $manager->flush();
    }

    private function getPurposeData(): array
    {
        return [
            'масова інформація',
            'освіта',
            'бізнес',
            'переписка',
        ];
    }

    private function getAccessLevelData(): array
    {
        return [
            'совсекретний' => [
                'секретний' => [
                    'дсв' => [
                        'відкритий',
                    ],
                ],
            ],
        ];
    }

    private function getExtensionData(): array
    {
        /** при добавлении новых файлов - не забудьте добавить физический тестовый такого же формата для фикстур */
        return [
            'текст' => ['doc', 'docx', 'txt', 'rtf'],
            'електронні таблиці' => ['xls', 'xlsx'],
            'рисунок' => ['jpg', 'png', 'gif', 'bmp'],
            'відео' => ['avi', 'flv'],
            'аудіо' => ['mp3', 'ogg'],
            'сайт' => [],
        ];
    }

    private function getUserAccesses(): array
    {
        return [
            'Рiвень А',
            'Рiвень B',
            'Рiвень C'
        ];
    }

    private function getUserGroups(): array
    {
        return [
            'Науковi працівники', 'Викладачi', 'Студенти'
        ];
    }

    private function getUserData(): array
    {
        return [
            // $userData = [$fullname, $username, $password, $email, $roles];
            ['Jane Doe', 'jane_admin', 'kitten', 'jane_admin@symfony.com', ['ROLE_ADMIN']],
            ['Jim Doe', 'jim_author', 'kitten', 'jim_author@symfony.com', ['ROLE_AUTHOR']],
            ['Jack Doe', 'jack_moderator', 'kitten', 'jack_moderator@symfony.com', ['ROLE_MODERATOR']],
            ['Tom Doe', 'tom_admin', 'kitten', 'tom_admin@symfony.com', ['ROLE_ADMIN']],
            ['John Doe', 'john_user', 'kitten', 'john_user@symfony.com', ['ROLE_USER']],
        ];
    }

    private function getKeywordData(): array
    {
        return [
            'lorem',
            'ipsum',
            'consectetur',
            'adipiscing',
            'incididunt',
            'labore',
            'voluptate',
            'dolore',
            'pariatur',
        ];
    }

    private function getResourceData()
    {
        $resources = [];
        foreach ($this->getPhrases() as $i => $title) {
            // $postData = [$title, $slug, $summary, $content, $publishedAt, $author, $tags, $purpose, $document, $accessLevel, $comments];
            $resources[] = [
                $title,
                Slugger::slugify($title),
                $this->getRandomText(),
                $this->getPostContent(),
                new \DateTime('now - ' . $i . 'days'),
                // Ensure that the first post is written by Jane Doe to simplify tests
                $this->getReference(['jane_admin', 'tom_admin'][0 === $i ? 0 : random_int(0, 1)]),
            ];
        }

        return $resources;
    }

    private function getPhrases(): array
    {
        return [
            'Lorem ipsum dolor sit amet consectetur adipiscing elit',
            'Pellentesque vitae velit ex',
            'Mauris dapibus risus quis suscipit vulputate',
            'Eros diam egestas libero eu vulputate risus',
            'In hac habitasse platea dictumst',
            'Morbi tempus commodo mattis',
            'Ut suscipit posuere justo at vulputate',
            'Ut eleifend mauris et risus ultrices egestas',
            'Aliquam sodales odio id eleifend tristique',
            'Urna nisl sollicitudin id varius orci quam id turpis',
            'Nulla porta lobortis ligula vel egestas',
            'Curabitur aliquam euismod dolor non ornare',
            'Sed varius a risus eget aliquam',
            'Nunc viverra elit ac laoreet suscipit',
            'Pellentesque et sapien pulvinar consectetur',
            'Ubi est barbatus nix',
            'Abnobas sunt hilotaes de placidus vita',
            'Ubi est audax amicitia',
            'Eposs sunt solems de superbus fortis',
            'Vae humani generis',
            'Diatrias tolerare tanquam noster caesium',
            'Teres talis saepe tractare de camerarius flavum sensorem',
            'Silva de secundus galatae demitto quadra',
            'Sunt accentores vitare salvus flavum parses',
            'Potus sensim ad ferox abnoba',
            'Sunt seculaes transferre talis camerarius fluctuies',
            'Era brevis ratione est',
            'Sunt torquises imitari velox mirabilis medicinaes',
            'Mineralis persuadere omnes finises desiderium',
            'Bassus fatalis classiss virtualiter transferre de flavum',
        ];
    }

    private function getRandomText(int $maxLength = 255): string
    {
        $phrases = $this->getPhrases();
        shuffle($phrases);

        while (mb_strlen($text = implode('. ', $phrases) . '.') > $maxLength) {
            array_pop($phrases);
        }

        return $text;
    }

    private function getPostContent(): string
    {
        return <<<'MARKDOWN'
Lorem ipsum dolor sit amet consectetur adipisicing elit, sed do eiusmod tempor
incididunt ut labore et **dolore magna aliqua**: Duis aute irure dolor in
reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
deserunt mollit anim id est laborum.

  * Ut enim ad minim veniam
  * Quis nostrud exercitation *ullamco laboris*
  * Nisi ut aliquip ex ea commodo consequat

Praesent id fermentum lorem. Ut est lorem, fringilla at accumsan nec, euismod at
nunc. Aenean mattis sollicitudin mattis. Nullam pulvinar vestibulum bibendum.
Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos
himenaeos. Fusce nulla purus, gravida ac interdum ut, blandit eget ex. Duis a
luctus dolor.

Integer auctor massa maximus nulla scelerisque accumsan. *Aliquam ac malesuada*
ex. Pellentesque tortor magna, vulputate eu vulputate ut, venenatis ac lectus.
Praesent ut lacinia sem. Mauris a lectus eget felis mollis feugiat. Quisque
efficitur, mi ut semper pulvinar, urna urna blandit massa, eget tincidunt augue
nulla vitae est.

Ut posuere aliquet tincidunt. Aliquam erat volutpat. **Class aptent taciti**
sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi
arcu orci, gravida eget aliquam eu, suscipit et ante. Morbi vulputate metus vel
ipsum finibus, ut dapibus massa feugiat. Vestibulum vel lobortis libero. Sed
tincidunt tellus et viverra scelerisque. Pellentesque tincidunt cursus felis.
Sed in egestas erat.

Aliquam pulvinar interdum massa, vel ullamcorper ante consectetur eu. Vestibulum
lacinia ac enim vel placerat. Integer pulvinar magna nec dui malesuada, nec
congue nisl dictum. Donec mollis nisl tortor, at congue erat consequat a. Nam
tempus elit porta, blandit elit vel, viverra lorem. Sed sit amet tellus
tincidunt, faucibus nisl in, aliquet libero.
MARKDOWN;
    }

    private function getRandomKeywords(): array
    {
        $keywordNames = $this->getKeywordData();
        shuffle($keywordNames);
        $selectedTags = \array_slice($keywordNames, 0, random_int(2, 4));

        return array_map(function ($tagName) {
            return $this->getReference('keyword-' . $tagName);
        }, $selectedTags);
    }

    private function getRandomPurpose(): MetaPurpose
    {
        $data = $this->getPurposeData();
        shuffle($data);

        return $this->getReference('purpose-' . current($data));
    }

    private function getRandomUserGroup(): UserGroup
    {
        $list = $this->references[UserGroup::class];
        shuffle($list);

        return $this->getReference(current($list));
    }

    private function getRandomCategory(): MetaCategory
    {
        $list = $this->references[MetaCategory::class];
        shuffle($list);

        return $this->getReference(current($list));
    }

    private function getRandomLanguage(): string
    {
        shuffle($this->languages);

        return current($this->languages);
    }

    private function getRandomDocument(): MetaDocumentType
    {
        $data = $this->getDocumentTypeData();
        shuffle($data);

        return $this->getReference('document-' . current($data));
    }

    private function getRandomMedia(): MetaMedia
    {
        $data = array_keys($this->getExtensionData());
        shuffle($data);
        do {
            /** @var MetaMedia $media */
            $media = $this->getReference('media-' . current($data));
            if ($media->getExtensions()->count()) {
                return $media;
            }
        } while (next($data));

        throw new \Exception('Available media not found');
    }

    private function getRandomFromClassName(string $className)
    {

        $list = $this->references[$className];
        shuffle($list);

        return $this->getReference(current($list));
    }

    private function getRandomAccessLevel(): MetaAccessLevel
    {

        $list = $this->references[MetaAccessLevel::class];
        shuffle($list);

        return $this->getReference(current($list));
    }

    protected function _slug(string $name)
    {

        return str_replace(' ', '_', mb_strtolower(trim($name)));
    }
}
