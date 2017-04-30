<?php

namespace Core\File;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\FileIterator;
use Concrete\Core\File\Type\Type;
use Concrete\Tests\Core\File\FileListTest;

class FileIteratorTest extends FileListTest
{

    public function testIteratesOverFiles()
    {
        $iterator = $this->files();
        $this->assertCount(11, $iterator->all());
    }

    public function testSortsByName()
    {
        $iterator = $this->files();
        $result = iterator_to_array($this->names($iterator->sortByName()->getIterator()));

        $sorted = array_values($result);
        sort($sorted);

        $this->assertSame($sorted, $result);
    }

    public function testSortsByNameReversed()
    {
        $iterator = $this->files();
        $result = iterator_to_array($this->names($iterator->sortByName('desc')->getIterator()));

        $sorted = array_values($result);
        rsort($sorted);

        $this->assertSame($sorted, $result);
    }

    public function testPaginates()
    {
        $iterator = $this->files()->sortByName();

        $page1 = iterator_to_array($this->names($iterator->page(3, 1)));
        $page2 = iterator_to_array($this->names($iterator->page(3, 2)));
        $page3 = iterator_to_array($this->names($iterator->page(3, 3)));
        $page4 = iterator_to_array($this->names($iterator->page(3, 4)));

        $this->assertSame(['awesome.txt', 'foobley.png', 'logo1.png'], $page1);
        $this->assertSame(['logo2.png', 'logo3.png', 'sample1.txt'], $page2);
        $this->assertSame(['sample2.txt', 'sample4.txt', 'sample5.txt'], $page3);
        $this->assertSame(['test.png', 'testing.txt'], $page4);
    }

    public function testGivesAll()
    {
        $iterator = $this->files();
        $all = $iterator->all();

        $this->assertTrue(is_array($all));
        $this->assertCount(11, $all);
    }

    public function testLimits()
    {
        $iterator = $this->files();
        $this->assertCount(5, $iterator->limit(5)->all());
    }

    public function testFirst()
    {
        $iterator = $this->files();
        $first = $iterator->first();

        $this->assertArraySubset(['fID' => '1'], $first);
    }

    public function testStreaming()
    {
        $iterator = $this->files()->limit(3)->sortByName();

        $first = iterator_to_array($this->names($iterator));
        $second = iterator_to_array($this->names($iterator->startingWith(function($item) use ($first) {
            return $item['fvFilename'] === $first[0];
        })));

        $this->assertSame($first, $second);
    }

    public function testFilterByExtension()
    {
        $types = ['txt', 'png'];

        $iterator = $this->files();
        foreach ($types as $type) {
            $names = iterator_to_array($this->names($iterator->filterByExtension($type)));
            $extensions = array_unique(array_map(function ($name) {
                return pathinfo($name, PATHINFO_EXTENSION);
            }, $names));

            $this->assertSame([$type], $extensions);
        }
    }

    public function testEasyFiltering()
    {
        $iterated = 0;
        // This should only iterate 3 times, since there are only 3 items after item 9
        $iterator = $this->files()->startingWith(9)->addWrapper(function(\Iterator $iterator) use (&$iterated) {
            foreach ($iterator as $item) {
                $iterated++;
                if ($item['fID'] % 2) {
                    yield $item;
                }
            }
        });

        $this->assertSame(['9', '11'], $this->ids($iterator));
        $this->assertEquals(3, $iterated);
    }

    public function testFilterByAttribute()
    {
        $key = FileKey::getByHandle('width');

        $this->assertCount(5, $this->files()->filterByAttribute($key, 100, '>')->all());
        $this->assertCount(0, $this->files()->filterByAttribute($key, 100, '<')->all());
    }

    public function testFilterByAttributeComparison()
    {
        $key = FileKey::getByHandle('width');

        $this->assertCount(5, $this->files()->filterByAttribute($key, 113, '=')->all());
        $this->assertCount(5, $this->files()->filterByAttribute($key, 113, '==')->all());

        $this->assertCount(5, $this->files()->filterByAttribute($key, 100, '>')->all());
        $this->assertCount(5, $this->files()->filterByAttribute($key, 113, '>=')->all());
        $this->assertCount(5, $this->files()->filterByAttribute($key, 113, '=>')->all());

        $this->assertCount(0, $this->files()->filterByAttribute($key, 100, '<')->all());
        $this->assertCount(5, $this->files()->filterByAttribute($key, 113, '<=')->all());
        $this->assertCount(5, $this->files()->filterByAttribute($key, 113, '=<')->all());

        $this->assertCount(5, $this->files()->filterByAttribute($key, '%113%', 'like')->all());
        $this->assertCount(0, $this->files()->filterByAttribute($key, '%113%', 'not like')->all());

        $this->assertCount(5, $this->files()->filterByAttribute($key, [1,2,3,113], 'in')->all());
        $this->assertCount(0, $this->files()->filterByAttribute($key, [1,2,3,113], 'not in')->all());
    }

    public static function assertSame($expected, $actual, $message = '')
    {
        if ($expected instanceof \Generator) {
            $expected = iterator_to_array($expected);
        }
        if ($actual instanceof \Generator) {
            $actual = iterator_to_array($actual);
        }

        parent::assertSame($expected, $actual, $message);
    }

    private function files()
    {
        return \Core::make(FileIterator::class);
    }

    private function ids(\Traversable $files)
    {
        foreach ($files as $file) {
            if ($file instanceof File) {
                yield $file->getFileID();
            } else {
                yield $file['fID'];
            }
        }
    }

    private function names(\Traversable $files)
    {
        foreach ($files as $file) {
            if ($file instanceof File) {
                yield $file->getVersion()->getFileName();
            } else {
                yield $file['fvFilename'];
            }
        }
    }

    public function testFilterByTypeValid1()
    {
        $this->assertCount(5, $this->files()->filterByType(Type::T_IMAGE)->all());
        $this->assertCount(6, $this->files()->filterByType(Type::T_TEXT)->all());
    }

    public function testFilterByExtensionAndType()
    {
        $this->assertCount(6, $this->files()
            ->filterByType(Type::T_TEXT)
            ->filterByExtension('txt')
            ->all());
    }

    public function testFilterByKeywords()
    {
        $this->assertCount(5, $this->files()->filterByKeyword('le')->all());
    }

    public function testFilterBySet()
    {
        $fs = \FileSet::add('test');
        $f = \File::getByID(1);
        $f2 = \File::getByID(4);
        $fs->addFileToSet($f);
        $fs->addFileToSet($f2);

        $fs2 = \FileSet::add('test2');
        $fs2->addFiletoSet($f);

        // Files in the sets
        $this->assertCount(2, $this->files()->filterBySet($fs)->all());
        $this->assertCount(1, $this->files()->filterBySet($fs2)->all());

        // Files not in any set
        $this->assertCount(9, $this->files()->filterBySet(null)->all());
    }

    public function testSortByFilename()
    {
        $this->assertEquals('awesome.txt', $this->files()->sortByName('asc')->first()['fvFilename']);
        $this->assertEquals('testing.txt', $this->files()->sortByName('desc')->first()['fvFilename']);
    }

    public function testSortByAttribute()
    {
        $this->connection()->exec('UPDATE FileSearchIndexAttributes SET ak_width=10000 where fID=10');
        $this->connection()->exec('UPDATE FileSearchIndexAttributes SET ak_width=1 where fID=9');

        $key = FileKey::getByHandle('width');
        $this->assertEquals('9', $this->files()->filterByType(Type::T_IMAGE)->sortByAttribute($key, 'asc')->first()['fID']);
        $this->assertEquals('10', $this->files()->filterByType(Type::T_IMAGE)->sortByAttribute($key, 'desc')->first()['fID']);

        $this->connection()->exec('UPDATE FileSearchIndexAttributes SET ak_width=113 where ak_width > 0');
    }

    public function testAutoSort()
    {
        // Not supported
    }

    public function testPaginationPagesWithoutPermissions()
    {
        // Not supported
    }

    public function testPaginationWithPermissions()
    {
        // Not supported
    }

    public function testFileSearchDefaultColumnSet()
    {
        // Not supported
    }

    public function testGetPaginationObject()
    {
        // Not supported
    }

}
