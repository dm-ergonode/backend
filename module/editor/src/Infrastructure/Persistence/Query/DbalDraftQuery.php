<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Editor\Infrastructure\Persistence\Query;

use Doctrine\DBAL\Connection;
use Ergonode\Editor\Domain\Query\DraftQueryInterface;
use Ergonode\SharedKernel\Domain\Aggregate\AttributeId;
use Ergonode\SharedKernel\Domain\Aggregate\ProductDraftId;
use Ergonode\SharedKernel\Domain\Aggregate\ProductId;

class DbalDraftQuery implements DraftQueryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     */
    public function getDraftView(ProductDraftId $draftId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $result = $qb->select('d.id AS draft_id, p.id AS product_id, pd.template_id, p.sku')
            ->from('designer.draft', 'd')
            ->join('d', 'product', 'p', 'p.id = d.product_id')
            ->join('d', 'designer.product', 'pd', 'pd.product_id = d.product_id')
            ->where($qb->expr()->eq('d.id', ':id'))
            ->setParameter(':id', $draftId->getValue())
            ->execute()
            ->fetch();

        $result['values'] = $this->getDraftValues($draftId);
        $result['category_ids'] = $this->getCategories($draftId);

        return $result;
    }

    public function getActualDraftId(ProductId $productId): ?ProductDraftId
    {
        $qb = $this->connection->createQueryBuilder();
        $result = $qb->select('id')
            ->from('designer.draft')
            ->andWhere($qb->expr()->eq('product_id', ':productId'))
            ->andWhere($qb->expr()->eq('applied', ':applied'))
            ->setParameter(':productId', $productId->getValue())
            ->setParameter(':applied', false, \PDO::PARAM_BOOL)
            ->setMaxResults(1)
            ->execute()
            ->fetchColumn();

        if ($result) {
            return new ProductDraftId($result);
        }

        return null;
    }

    /**
     * @return ProductDraftId[]
     */
    public function getNotAppliedWithAttribute(AttributeId $attributeId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $data = $qb->select('d.id')
            ->from('designer.draft_value', 'dv')
            ->join('dv', 'designer.draft', 'd', 'd.id =dv.draft_id')
            ->andWhere($qb->expr()->eq('d.applied', ':applied'))
            ->andWhere($qb->expr()->eq('dv.element_id', ':attributeId'))
            ->setParameter(':applied', false, \PDO::PARAM_BOOL)
            ->setParameter(':attributeId', $attributeId->getValue())
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);

        $result = [];
        if ($data) {
            foreach ($data as $productDraftId) {
                $result[] = new ProductDraftId($productDraftId);
            }
        }

        return $result;
    }

    public function getProductId(ProductDraftId $id): ProductId
    {
        $qb = $this->connection->createQueryBuilder();
        $result = $qb->select('d.product_id')
            ->from('designer.draft', 'd')
            ->andWhere($qb->expr()->eq('d.id', ':draftId'))
            ->setParameter(':draftId', $id->getValue())
            ->execute()
            ->fetch(\PDO::FETCH_COLUMN);

        return new ProductId($result);
    }

    /**
     * @return array
     */
    private function getDraftValues(ProductDraftId $draftId): array
    {
        $qb = $this->connection->createQueryBuilder();

        return $qb
            ->select('dv.element_id AS id, dv.value')
            ->from('designer.draft_value', 'dv')
            ->where($qb->expr()->eq('dv.draft_id', ':draftId'))
            ->setParameter(':draftId', $draftId->getValue())
            ->execute()
            ->fetchAll();
    }

    /**
     * @return array
     */
    private function getCategories(ProductDraftId $draftId): array
    {
        $qb = $this->connection->createQueryBuilder();

        return $qb->select('category_id')
            ->from('product_category', 'pcp')
            ->join('pcp', 'designer.draft', 'd', 'd.product_id = pcp.product_id')
            ->where($qb->expr()->eq('d.id', ':id'))
            ->setParameter(':id', $draftId->getValue())
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);
    }
}
